<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\RecaptchaTrait;

class LoginRequest extends FormRequest
{
        use RecaptchaTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'device_name' => 'string|max:255',
            'remember_me' => 'boolean',
            'recaptcha_token' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'recaptcha_token.required' => 'Please complete the CAPTCHA.',
        ];
    }

    /**
     * Enhanced reCAPTCHA validation
     */
    protected function passedValidation(): void
    {
        $this->validateRecaptcha(
            $this->input('recaptcha_token'),
            $this->ip()
        );
    }

}
