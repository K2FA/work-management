<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;

/**
 * Custom login page logic for Filament panels.
 *
 * Handles authentication, rate limiting, role-based panel switching,
 * and session regeneration upon successful login.
 */
class LoginCustom extends BaseLogin
{
    /**
     * Handle the authentication process.
     *
     * @return LoginResponse|null
     *
     * @throws ValidationException
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            // Apply rate limiting (5 attempts)
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            // If user is rate limited, notify and stop login
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        // Retrieve form data (email, password, remember checkbox, etc.)
        $data = $this->form->getState();

        // Attempt authentication
        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            // Throw validation error if credentials are invalid
            $this->throwFailureValidationException();
        }

        // Get the authenticated user
        $user = Filament::auth()->user();

        // Set the panel based on the user's role
        if ($user->hasRole('Admin')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
        } elseif ($user->hasRole('Employee')) {
            Filament::setCurrentPanel(Filament::getPanel('employee'));
        } elseif ($user->hasRole('Manager')) {
            Filament::setCurrentPanel(Filament::getPanel('manager'));
        } else {
            // If the user has no recognized role, log them out and throw error
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // Regenerate the session to prevent session fixation
        session()->regenerate();

        // Return the appropriate login response
        return app(LoginResponse::class);
    }
}
