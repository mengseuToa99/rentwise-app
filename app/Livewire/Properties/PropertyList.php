<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;
use App\Models\UserDetail;
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
        $authUser = Auth::user();
        $userDetail = UserDetail::where('email', $authUser->email)->first();
        
        if (!$userDetail) {
            session()->flash('error', 'User profile not found');
            return view('livewire.properties.property-list', [
                'properties' => collect([])
            ]);
        }
        
        $query = Property::query();
        
        // If not admin, show only the landlord's properties
        $userRoles = $userDetail->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin')) {
            $query->where('landlord_id', $userDetail->user_id);
        }
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('property_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
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
        $property = Property::find($propertyId);
        
        if (!$property) {
            session()->flash('error', 'Property not found');
            return;
        }
        
        // Verify ownership
        $authUser = Auth::user();
        $userDetail = UserDetail::where('email', $authUser->email)->first();
        
        if (!$userDetail) {
            session()->flash('error', 'User profile not found');
            return;
        }
        
        $userRoles = $userDetail->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin') && $property->landlord_id !== $userDetail->user_id) {
            session()->flash('error', 'You are not authorized to delete this property');
            return;
        }
        
        try {
            // Delete associated units first
            $property->units()->delete();
            
            // Delete the property
            $property->delete();
            
            session()->flash('success', 'Property deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete property: ' . $e->getMessage());
        }
    }
    
    public function landlordProperties()
    {
        $this->resetPage();
        return $this->render();
    }
} 