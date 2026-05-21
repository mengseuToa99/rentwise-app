<?php

namespace App\Livewire\Invoices;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InvoiceSimpleMode extends Component
{
    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        session(['simple_mode' => true]);

        return null;
    }

    public function exitSimpleMode()
    {
        session()->forget('simple_mode');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.invoices.invoice-simple-mode');
    }
}
