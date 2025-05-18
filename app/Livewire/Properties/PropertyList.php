<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PropertyList extends Component
{
    use WithPagination;
    
    public $search = '';
    
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
        
        if (!$user) {
            session()->flash('error', 'Authentication failed. Please log in again.');
            return redirect()->route('login');
        }
        
        $query = Property::query();
        
        // Always show only the landlord's properties (since admin can't access this page anymore)
        $query->where('landlord_id', $user->user_id);
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('property_name', 'like', '%' . $this->search . '%')
                  ->orWhere('house_building_number', 'like', '%' . $this->search . '%')
                  ->orWhere('street', 'like', '%' . $this->search . '%')
                  ->orWhere('village', 'like', '%' . $this->search . '%')
                  ->orWhere('commune', 'like', '%' . $this->search . '%')
                  ->orWhere('district', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get properties with related information
        $properties = $query->with(['units', 'landlord'])
            ->withCount(['units as total_units', 'units as occupied_units' => function($query) {
                $query->where('status', 'occupied');
            }])
            ->paginate(10);
        
        return view('livewire.properties.property-list', [
            'properties' => $properties
        ]);
    }
    
    public function deleteProperty($propertyId)
    {
        try {
            $property = Property::findOrFail($propertyId);
            
            // Check if user owns this property
            if ($property->landlord_id !== Auth::id()) {
                session()->flash('error', 'You do not have permission to delete this property.');
                return;
            }
            
            $property->delete();
            session()->flash('success', 'Property deleted successfully.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting property: ' . $e->getMessage());
        }
    }
    
    public function landlordProperties()
    {
        $this->resetPage();
        return $this->render();
    }
} 