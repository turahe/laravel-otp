<?php

namespace Turahe\Otp;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Turahe\Otp\Rules\EmailProvider;
use Turahe\Otp\Services\Token;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('otp', function () {
            return new Token;
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/otp.php',
            'otp'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/disposable-email-providers.php',
            'disposable-email-providers'
        );
    }

    public function boot()
    {
        $this->loadTranslationsFrom(
            dirname(__DIR__).'/resources/lang',
            'otp'
        );

        if (function_exists('config_path')) {
            $this->publishes([
                dirname(__DIR__).'/config/otp.php' => config_path('otp.php'),
            ], 'otp');
        }
        $this->loadViewsFrom(
            dirname(__DIR__).'/resources/views',
            'otp'
        );
        $this->loadMigrationsFrom([__DIR__.'/../database/migrations']);

        Validator::extend('email_provider', EmailProvider::class);
    }
}
