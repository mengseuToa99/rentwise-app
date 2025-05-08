<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FluxComponentsServiceProvider extends ServiceProvider
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
        // Register the components using the correct aliasing format
        Blade::component('components.flux.modal', 'flux.modal');
        Blade::component('components.flux.modal.header', 'flux.modal.header');
        Blade::component('components.flux.modal.content', 'flux.modal.content');
        Blade::component('components.flux.input', 'flux.input');
        Blade::component('components.flux.button', 'flux.button');
    }
} 