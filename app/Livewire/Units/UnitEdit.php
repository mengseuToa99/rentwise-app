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
    public $size;
    public $rentAmount;
    public $status;
    
    protected $rules = [
        'propertyId' => 'required|exists:property_detail,property_id',
        'roomNumber' => 'required|string|max:20',
        'type' => 'required|string|max:50',
        'size' => 'required|string|max:50',
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
        $this->size = $unit->size;
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
            $unit->size = $this->size;
            $unit->rent_amount = $this->rentAmount;
            $unit->status = $this->status;
            $unit->save();
            
            session()->flash('success', 'Unit updated successfully!');
            return redirect()->route('properties.show', $this->propertyId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating unit: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Get properties for the dropdown
        $user = Auth::user();
        $query = Property::query();
        
        // If not admin, show only properties owned by this landlord
        if (!$user->roles->contains('role_name', 'admin')) {
            $query->where('landlord_id', $user->user_id);
        }
        
        $properties = $query->pluck('name', 'property_id');
        
        return view('livewire.units.unit-edit', [
            'properties' => $properties
        ]);
    }
} 