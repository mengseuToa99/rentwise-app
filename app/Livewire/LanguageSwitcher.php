<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LanguageSwitcher extends Component
{
    public $currentLocale;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage()
    {
        $newLocale = $this->currentLocale === 'en' ? 'km' : 'en';
        
        // Update the locale
        App::setLocale($newLocale);
        Session::put('locale', $newLocale);
        $this->currentLocale = $newLocale;

        // Livewire actions post to /livewire/update; always redirect the user
        // back to the last real page URL instead of the update endpoint.
        $referer = request()->headers->get('referer');

        if (! $referer || Str::contains($referer, '/livewire/update')) {
            $referer = url()->previous();
        }

        if (! $referer || Str::contains($referer, '/livewire/update')) {
            $referer = route('dashboard');
        }

        return redirect($referer);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
} 
