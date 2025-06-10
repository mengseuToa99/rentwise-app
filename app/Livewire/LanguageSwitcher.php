<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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

        // Redirect to refresh the page with new locale
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
} 