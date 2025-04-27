<?php

namespace App\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }
        if ($user->hasRole('Employee')) {
            return redirect()->intended(route('filament.employee.pages.dashboard'));
        }
        if ($user->hasRole('Manager')) {
            return redirect()->intended(route('filament.manager.pages.dashboard'));
        }

        // Default fallback (kalau tidak punya role)
        return redirect()->route('home');
    }
}
