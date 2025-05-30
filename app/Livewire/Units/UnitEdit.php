<?php

namespace App\Livewire\Units;

use Livewire\Component;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class UnitEdit extends Component
{
    public $unitId;
    public $propertyId;
    public $roomNumber;
    public $type;
    public $rentAmount;
    public $status;
    
    protected $rules = [
        'propertyId' => 'required|exists:property_details,property_id',
        'roomNumber' => 'required|string|max:20',
        'type' => 'required|string|max:50',
        'rentAmount' => 'required|numeric|min:0',
        'status' => 'required|in:vacant,occupied',
    ];
    
    public function mount($unit)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->unitId = $unit;
        
        $unit = Unit::find($this->unitId);
        
        if (!$unit) {
            session()->flash('error', 'Unit not found');
            return redirect()->route('units.index');
        }
        
        // Check if user is authorized to edit this unit
        $user = Auth::user();
        $property = Property::find($unit->property_id);
        
        // if (!$property || (!$user->roles->contains('role_name', 'admin') && $property->landlord_id !== $user->user_id)) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        $this->propertyId = $unit->property_id;
        $this->roomNumber = $unit->room_number;
        $this->type = $unit->type;
        $this->rentAmount = $unit->rent_amount;
        $this->status = $unit->status;
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $unit = Unit::find($this->unitId);
            
            if (!$unit) {
                session()->flash('error', 'Unit not found');
                return redirect()->route('units.index');
            }
            
            // Check if user is authorized to edit this unit
            $user = Auth::user();
            $property = Property::find($this->propertyId);
            
            if (!$property || (!$user->roles->contains('role_name', 'admin') && $property->landlord_id !== $user->user_id)) {
                session()->flash('error', 'You are not authorized to edit this unit');
                return;
            }
            
            $unit->property_id = $this->propertyId;
            $unit->room_number = $this->roomNumber;
            $unit->type = $this->type;
            $unit->room_type = $this->type; // Ensure both type fields are updated
            $unit->rent_amount = $this->rentAmount;
            $unit->status = $this->status;
            $unit->save();
            
            session()->flash('success', 'Unit updated successfully!');
            return redirect()->route('units.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating unit: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Get properties for the dropdown
        $authUser = Auth::user();
        
        if (!$authUser) {
            return view('livewire.units.unit-edit', [
                'properties' => collect([])
            ]);
        }
        
        $query = Property::query()
            ->select(['property_id', 'property_name'])
            ->orderBy('property_name');
        
        // If not admin, show only properties owned by this landlord
        $userRoles = $authUser->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin')) {
            $query->where('landlord_id', $authUser->user_id);
        }
        
        // Get properties and log them for debugging
        $properties = $query->get();
        
        \Log::info('Properties for edit:', [
            'properties' => $properties->map(function($prop) {
                return [
                    'id' => $prop->property_id,
                    'name' => $prop->property_name
                ];
            })->toArray()
        ]);
        
        return view('livewire.units.unit-edit', [
            'properties' => $properties
        ]);
    }
} 