<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS if the request comes from an ngrok domain
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        URL::forceScheme('https');
    }

    // Alternative approach: check if the host contains ngrok
    // if (str_contains(request()->getHost(), 'ngrok-free.app')) {
    //     URL::forceScheme('https');
    // }
    }
}
