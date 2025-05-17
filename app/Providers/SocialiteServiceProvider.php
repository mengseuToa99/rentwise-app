<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        // Register Telegram provider through SocialiteProviders Manager
        $this->app->events->listen(SocialiteWasCalled::class, 'SocialiteProviders\\Telegram\\TelegramExtendSocialite@handle');
    }
} 