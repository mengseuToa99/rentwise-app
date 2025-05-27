<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use App\Models\User;
use App\Models\Rental;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class PropertyDetails extends Component
{
    public $property = null;
    public $unit = null;
    public $rental = null;

    public function mount()
    {
        $this->loadPropertyDetails();
    }

    protected function loadPropertyDetails()
    {
        $user = Auth::user();
        
        // Find the active rental for this tenant
        $rental = Rental::where('tenant_id', $user->user_id)
            ->where('status', 'active')
            ->with(['unit', 'unit.property'])
            ->first();
        
        if ($rental) {
            $this->rental = $rental;
            $this->unit = $rental->unit;
            $this->property = $rental->unit->property;
        }
    }

    public function render()
    {
        return view('livewire.tenants.property-details');
    }
} 