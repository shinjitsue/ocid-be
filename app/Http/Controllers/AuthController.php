<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Handle user login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Rate limiting
        $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => ["Too many login attempts. Please try again in {$seconds} seconds."],
            ]);
        }

        $user = User::active()->where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->getAuthPassword())) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user account is active
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        // Update last login info
        $user->updateLastLogin($request->ip());

        // Set user in session if using web middleware
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        // Token expiration based on remember_me
        $expirationDays = $request->boolean('remember_me') ? 30 : 1;
        $deviceName = $request->input('device_name', 'Unknown Device');

        // Revoke old tokens for single session
        if (!$request->boolean('remember_me')) {
            $user->tokens()->delete();
        }

        $token = $user->createToken($deviceName, ['*'], now()->addDays($expirationDays))->plainTextToken;

        // Log user activity
        $this->logUserActivity($user, 'login', $request, ['remember_me' => $request->boolean('remember_me')]);

        Log::info('User login attempt', [
            'user_id' => $user->getKey(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'remember_me' => $request->boolean('remember_me'),
            'timestamp' => now(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expirationDays * 24 * 60 * 60,
            'expires_at' => now()->addDays($expirationDays)->toISOString(),
        ]);


    }

    /**
     * Handle user registration
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Fire registered event for email verification
        event(new Registered($user));

        $deviceName = $request->input('device_name', 'Unknown Device');
        $token = $user->createToken($deviceName, ['*'], now()->addDays(30))->plainTextToken;

        // Log user activity
        $this->logUserActivity($user, 'register', $request);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
            'message' => 'Registration successful. Please check your email for verification.',
        ], 201);
    }

    /**
     * Handle email verification
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $user = User::findOrFail($request->input('id'));

        if (!hash_equals(sha1($user->getEmailForVerification()), $request->input('hash'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid verification link.'],
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully.']);
    }

    /**
     * Resend email verification
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json([
                'message' => 'If your email exists in our system, you will receive a verification email.'
            ]);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent successfully.'
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Log user activity
        $this->logUserActivity($request->user(), 'logout', $request);


        // Invalidate the token
        $request->user()->currentAccessToken()->delete();

        // Also invalidate the session if it exists
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Optionally logout from all other devices
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        // Log user activity
        $this->logUserActivity($user, 'password_change', $request);

        return response()->json(['message' => 'Password changed successfully']);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                // Revoke all existing tokens
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            // Don't reveal if email exists
            return response()->json([
                'message' => 'If your email exists in our system, you will receive a password reset link.'
            ]);
        }

        $token = Password::createToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return response()->json([
            'message' => 'If your email exists in our system, you will receive a password reset link.'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255|min:2',
            'preferences' => 'sometimes|array',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'preferences']));

        return response()->json([
            'user' => $user->fresh(),
            'message' => 'Profile updated successfully',
        ]);
    }

    /**
     * Log user activity
     */
    protected function logUserActivity(User $user, string $activityType, Request $request, array $metadata = []): void
    {
        UserActivity::create([
            'user_id' => $user->getKey(),
            'activity_type' => $activityType,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => array_merge($metadata, [
                'timestamp' => now()->toISOString(),
            ]),
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $currentToken = $request->user()->currentAccessToken();

        // Delete current token
        $currentToken->delete();

        // Create new token with same name and expiration
        $deviceName = $currentToken->name ?? 'Unknown Device';
        $expiresAt = $currentToken->expires_at ?? now()->addDays(30);
        $token = $user->createToken($deviceName, ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresAt->diffInSeconds(now()),
        ]);
    }
}
