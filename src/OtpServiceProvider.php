<?php

namespace Turahe\Otp;

use Turahe\Otp\Services\Otp;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('otp', function () {
            return new Otp;
        });
    }

    public function boot()
    {
        $this->loadTranslationsFrom(
            dirname(__DIR__) . '/resources/lang',
            'otp'
        );

        if (function_exists('config_path')) {
            $this->publishes([
                dirname(__DIR__) . '/config/otp.php' => config_path('otp.php'),
            ], 'otp');
        }
        $this->loadViewsFrom(
            dirname(__DIR__) . '/resources/views',
            'otp'
        );
        $this->loadMigrationsFrom([__DIR__ . '/../database/migrations']);
    }
}
