<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(FluxComponentsServiceProvider::class);
        $this->app->register(SocialiteServiceProvider::class);

        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The bundled Khmer (km) Carbon locale defines relative-time units with
        // plural rules covering only {1} (exactly one) and ]1,Inf[ (more than one).
        // It has no rule for 0, so Carbon's diffForHumans() throws
        // "Unable to choose a translation ... for value 0" whenever a timestamp is
        // less than one unit old (e.g. a chat message "0 seconds" ago). This only
        // surfaced in Khmer; English already has a 0 rule. Patch the km unit strings
        // so the first plural option also matches 0 ([0,1] instead of {1}).
        Carbon::getTranslator()->setMessages('km', [
            'year'   => '[0,1]មួយឆ្នាំ|]1,Inf[:count ឆ្នាំ',
            'month'  => '[0,1]មួយខែ|]1,Inf[:count ខែ',
            'day'    => '[0,1]មួយថ្ងៃ|]1,Inf[:count ថ្ងៃ',
            'hour'   => '[0,1]មួយម៉ោង|]1,Inf[:count ម៉ោង',
            'minute' => '[0,1]មួយនាទី|]1,Inf[:count នាទី',
            'second' => '[0,1]ប៉ុន្មានវិនាទី|]1,Inf[:count វិនាទី',
        ]);
    }
}
