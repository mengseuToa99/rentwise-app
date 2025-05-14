<?php

namespace App\Livewire\PricingGroups;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Property;
use App\Models\PricingGroup;
use Illuminate\Support\Facades\Auth;

class PricingGroupList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $property;
    public $propertyId;
    
    public function mount($property)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->property = Property::findOrFail($property);
        $this->propertyId = $this->property->property_id;
        
        // Check if the authenticated user owns this property
        if ($this->property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'You do not have permission to view pricing groups for this property.');
            return redirect()->route('properties.index');
        }
    }
    
    public function render()
    {
        $query = PricingGroup::query();
        
        // Filter by property
        $query->where('property_id', $this->propertyId);
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('group_name', 'like', '%' . $this->search . '%')
                  ->orWhere('room_type', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get pricing groups with related units
        $pricingGroups = $query->withCount('units')
            ->paginate(10);
            
        return view('livewire.pricing-groups.pricing-group-list', [
            'pricingGroups' => $pricingGroups,
            'property' => $this->property
        ]);
    }
    
    public function deletePricingGroup($groupId)
    {
        $pricingGroup = PricingGroup::find($groupId);
        
        if (!$pricingGroup) {
            session()->flash('error', 'Pricing group not found');
            return;
        }
        
        // Verify ownership through property
        $property = Property::find($pricingGroup->property_id);
        
        if (!$property) {
            session()->flash('error', 'Associated property not found');
            return;
        }
        
        if ($property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'You are not authorized to delete this pricing group');
            return;
        }
        
        try {
            // Set pricing_group_id to null for all units using this group
            foreach ($pricingGroup->units as $unit) {
                $unit->pricing_group_id = null;
                $unit->save();
            }
            
            // Delete the pricing group
            $pricingGroup->delete();
            
            session()->flash('success', 'Pricing group deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete pricing group: ' . $e->getMessage());
        }
    }
}
