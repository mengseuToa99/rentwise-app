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
    public $perPage = 10; // Default number of units per page
    
    protected $queryString = ['search', 'propertyFilter', 'availabilityFilter', 'perPage'];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function refresh()
    {
        // This method will be called to refresh the component
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        $query = Unit::query();
        
        // Join with properties to access property data
        $query->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select([
                'room_details.*',
                'property_details.property_name',
                'room_details.room_type as type',
                'room_details.status',
                'room_details.available'
            ])
            ->latest('room_details.updated_at'); // Order by latest updated first
        
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
            if ($this->availabilityFilter === 'available') {
                $query->where('room_details.status', 'vacant');
            } else {
                $query->where('room_details.status', 'occupied');
            }
        }
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('room_details.room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('room_details.room_type', 'like', '%' . $this->search . '%')
                  ->orWhere('room_details.room_name', 'like', '%' . $this->search . '%')
                  ->orWhere('property_details.property_name', 'like', '%' . $this->search . '%');
            });
        }
        
        // Order by property name and room number
        $query->orderBy('property_details.property_name')
              ->orderBy('room_details.room_number');
        
        // Get units with pagination
        $units = $this->perPage === 'all' 
            ? $query->get() 
            : $query->paginate($this->perPage);
        
        // Get properties for the filter dropdown
        $properties = Property::query();
        if (!$userRoles->contains('role_name', 'admin')) {
            $properties->where('landlord_id', $user->user_id);
        }
        $properties = $properties->orderBy('property_name')->pluck('property_name', 'property_id');
        
        // Create array of pagination options
        $paginationOptions = [
            10 => '10 per page',
            25 => '25 per page',
            50 => '50 per page',
            100 => '100 per page',
            'all' => 'Show All'
        ];
        
        return view('livewire.units.unit-list', [
            'units' => $units,
            'properties' => $properties,
            'paginationOptions' => $paginationOptions
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