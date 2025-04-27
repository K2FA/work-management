<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;

class LoginCustom extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if ($user->hasRole('Admin')) {
            Filament::setCurrentPanel(Filament::getPanel('admin'));
        } else if ($user->hasRole('Employee')) {
            Filament::setCurrentPanel(Filament::getPanel('employee'));
        } else if ($user->hasRole('Manager')) {
            Filament::setCurrentPanel(Filament::getPanel('manager'));
        } else {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
