<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

trait RecaptchaTrait
{
    /**
     * Validate reCAPTCHA token
     */
    protected function validateRecaptcha(string $token, string $ip): void
    {
        $secretKey = config('services.recaptcha.secret');

        if (!$secretKey) {
            throw ValidationException::withMessages([
                'recaptcha_token' => ['reCAPTCHA configuration is missing.'],
            ]);
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            $result = $response->json();

            // Check if the request was successful
            if (!$response->successful()) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['Unable to verify CAPTCHA. Please try again.'],
                ]);
            }

            // Check reCAPTCHA success
            if (!($result['success'] ?? false)) {
                $errorCodes = $result['error-codes'] ?? [];

                // Handle specific error codes
                if (in_array('timeout-or-duplicate', $errorCodes)) {
                    $message = 'CAPTCHA has expired. Please try again.';
                } elseif (in_array('invalid-input-response', $errorCodes)) {
                    $message = 'Invalid CAPTCHA response. Please try again.';
                } else {
                    $message = 'CAPTCHA verification failed. Please try again.';
                }

                throw ValidationException::withMessages([
                    'recaptcha_token' => [$message],
                ]);
            }

            // Optional: Check score for reCAPTCHA v3 (if you're using v3)
            $score = $result['score'] ?? null;
            if ($score !== null && $score < 0.5) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => ['Security verification failed. Please try again.'],
                ]);
            }

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage());

            throw ValidationException::withMessages([
                'recaptcha_token' => ['Unable to verify CAPTCHA. Please try again.'],
            ]);
        }
    }
}
