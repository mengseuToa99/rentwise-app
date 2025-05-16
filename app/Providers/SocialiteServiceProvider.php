<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Add custom Telegram driver
        Socialite::extend('telegram', function ($app) {
            return Socialite::buildProvider(
                \App\Services\Auth\TelegramProvider::class,
                config('services.telegram')
            );
        });
    }
} 