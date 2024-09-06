<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * LoginRequest
 * 
 * Handles validation and authentication for user login requests. This class
 * ensures that login attempts are validated and rate-limited to prevent abuse.
 * 
 * @package App\Http\Requests\Auth
 */

class LoginRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Ensure the request is not rate limited
        $this->ensureIsNotRateLimited();

        // Attempt to authenticate the user with the provided email and password
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // If authentication fails, hit the rate limiter
            RateLimiter::hit($this->throttleKey());

            // Throw validation exception with failure message
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Clear the rate limiter if authentication is successful
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Check if the number of attempts exceeds the limit
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Trigger lockout event
        event(new Lockout($this));

        // Get the number of seconds until the request can be retried
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Throw validation exception with throttle message
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // Generate a unique throttle key based on the email and IP address
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
