<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS en producción para todos los helpers de URL
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
