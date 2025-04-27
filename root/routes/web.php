<?php

use App\Http\Controllers\CustomLogoutController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::permanentRedirect('/', '/login');

Route::post('/admin/logout', [CustomLogoutController::class, 'logout'])->name('filament.admin.auth.logout');
Route::post('/manager/logout', [CustomLogoutController::class, 'logout'])->name('filament.manager.auth.logout');
Route::post('/employee/logout', [CustomLogoutController::class, 'logout'])->name('filament.employee.auth.logout');
