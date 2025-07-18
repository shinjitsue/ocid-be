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
        // Check if reCAPTCHA is enabled
        if (!config('services.recaptcha.enabled', false)) {
            \Log::info('reCAPTCHA validation skipped - disabled in configuration');
            return;
        }

        $secretKey = config('services.recaptcha.secret');

        if (!$secretKey) {
            \Log::error('reCAPTCHA configuration error', [
                'secret_exists' => !empty($secretKey),
                'config_path' => 'services.recaptcha.secret',
                'env_value' => env('RECAPTCHA_SECRET_KEY') ? 'present' : 'missing'
            ]);

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
                \Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                throw ValidationException::withMessages([
                    'recaptcha_token' => ['Unable to verify CAPTCHA. Please try again.'],
                ]);
            }

            // Check reCAPTCHA success
            if (!($result['success'] ?? false)) {
                $errorCodes = $result['error-codes'] ?? [];

                \Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $errorCodes,
                    'result' => $result
                ]);

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

            \Log::info('reCAPTCHA verification successful');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage(), [
                'exception' => $e,
                'token_length' => strlen($token),
                'ip' => $ip
            ]);

            throw ValidationException::withMessages([
                'recaptcha_token' => ['Unable to verify CAPTCHA. Please try again.'],
            ]);
        }
    }
}
