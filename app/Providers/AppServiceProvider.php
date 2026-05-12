<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Auto-detect ngrok atau tunnel lainnya
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            URL::forceRootUrl('https://' . $_SERVER['HTTP_X_FORWARDED_HOST']);
        }

        if (app()->environment('local')) {
            header('ngrok-skip-browser-warning: true');
        }
    }
}