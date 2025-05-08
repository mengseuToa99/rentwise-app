<?php

namespace App\Livewire\Units;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class UnitList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $propertyFilter = '';
    public $availabilityFilter = '';
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }
    
    public function render()
    {
        $user = Auth::user();
        $query = Unit::query();
        
        // Join with properties to access property data
        $query->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select('room_details.*', 'property_details.property_name as property_name');
        
        // If not admin, show only the units of properties owned by this landlord
        $userRoles = $user->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin')) {
            $query->where('property_details.landlord_id', $user->user_id);
        }
        
        // Apply property filter
        if (!empty($this->propertyFilter)) {
            $query->where('room_details.property_id', $this->propertyFilter);
        }
        
        // Apply availability filter
        if ($this->availabilityFilter !== '') {
            $available = ($this->availabilityFilter === 'available');
            $query->where('room_details.available', $available);
        }
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('room_details.room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('room_details.room_type', 'like', '%' . $this->search . '%')
                  ->orWhere('property_details.property_name', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get units with their property information
        $units = $query->with('property')->paginate(10);
        
        // Get properties for the filter dropdown
        $properties = Property::query();
        $userRoles = $user->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin')) {
            $properties->where('landlord_id', $user->user_id);
        }
        $properties = $properties->pluck('property_name', 'property_id');
        
        return view('livewire.units.unit-list', [
            'units' => $units,
            'properties' => $properties
        ]);
    }
    
    public function deleteUnit($unitId)
    {
        try {
            $unit = Unit::find($unitId);
            
            if (!$unit) {
                session()->flash('error', 'Unit not found');
                return;
            }
            
            // Verify authorization by checking property ownership
            $user = Auth::user();
            $property = Property::find($unit->property_id);
            
            $userRoles = $user->roles ?? collect([]);
            if (!$userRoles->contains('role_name', 'admin') && $property->landlord_id !== $user->user_id) {
                session()->flash('error', 'You are not authorized to delete this unit');
                return;
            }
            
            $unit->delete();
            
            session()->flash('success', 'Unit deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete unit: ' . $e->getMessage());
        }
    }
} 