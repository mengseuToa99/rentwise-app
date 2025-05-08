<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PropertyDetail extends Component
{
    public $propertyId;
    public $property;
    public $units = [];
    public $totalUnits = 0;
    public $occupiedUnits = 0;
    public $vacantUnits = 0;
    
    public function mount($property)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->propertyId = $property;
        $this->loadProperty();
    }
    
    public function loadProperty()
    {
        $authUser = Auth::user();
        
        if (!$authUser) {
            session()->flash('error', 'User profile not found');
            return redirect()->route('properties.index');
        }
        
        // Fetch the property with related data
        $property = Property::with(['landlord', 'units'])
            ->where('property_id', $this->propertyId)
            ->first();
        
        if (!$property) {
            session()->flash('error', 'Property not found');
            return redirect()->route('properties.index');
        }
        
        // Check if the user is authorized to view this property
        $userRoles = $authUser->roles ?? collect([]);
        // if (!$userRoles->contains('role_name', 'admin') && $property->landlord_id !== $authUser->user_id) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        $this->property = $property;
        $this->totalUnits = $property->units->count();
        $this->occupiedUnits = $property->units->where('status', 'occupied')->count();
        $this->vacantUnits = $property->units->where('status', 'vacant')->count();
        
        // Format the units for display
        $this->units = $property->units->map(function($unit) {
            return [
                'id' => $unit->room_id,
                'number' => $unit->room_number,
                'type' => $unit->type,
                'size' => $unit->size,
                'rent' => $unit->rent_amount,
                'status' => $unit->status
            ];
        })->toArray();
    }
    
    public function deleteUnit($unitId)
    {
        try {
            $unit = Unit::find($unitId);
            
            if (!$unit || $unit->property_id != $this->propertyId) {
                session()->flash('error', 'Unit not found');
                return;
            }
            
            $unit->delete();
            
            session()->flash('success', 'Unit deleted successfully');
            $this->loadProperty();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete unit: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.properties.property-detail');
    }
} 