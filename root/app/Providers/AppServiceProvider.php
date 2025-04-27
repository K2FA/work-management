<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use App\Http\Responses\Auth\CustomLoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
