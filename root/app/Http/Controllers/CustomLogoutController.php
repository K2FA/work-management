<?php

namespace App\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\Request;

class CustomLogoutController extends Controller
{
    public function logout(Request $request)
    {
        Filament::auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
