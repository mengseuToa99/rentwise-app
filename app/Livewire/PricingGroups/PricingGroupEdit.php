<?php

namespace App\Livewire\PricingGroups;

use Livewire\Component;
use App\Models\Property;
use App\Models\PricingGroup;
use Illuminate\Support\Facades\Auth;

class PricingGroupEdit extends Component
{
    public $property;
    public $propertyId;
    public $group;
    public $groupId;
    
    public $group_name;
    public $room_type;
    public $base_price;
    public $description;
    public $amenities = [];
    public $status;
    
    protected $rules = [
        'group_name' => 'required|string|max:255',
        'room_type' => 'required|string|max:255',
        'base_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'amenities' => 'nullable|array',
        'status' => 'required|in:active,inactive'
    ];
    
    public function mount($property, $group)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->property = Property::findOrFail($property);
        $this->propertyId = $this->property->property_id;
        
        // Check if the authenticated user owns this property
        if ($this->property->landlord_id != Auth::id() && !Auth::user()->hasRole('admin')) {
            session()->flash('error', 'You do not have permission to edit pricing groups for this property.');
            return redirect()->route('properties.index');
        }
        
        $this->group = PricingGroup::findOrFail($group);
        $this->groupId = $this->group->group_id;
        
        // Make sure the pricing group belongs to the specified property
        if ($this->group->property_id != $this->propertyId) {
            session()->flash('error', 'The specified pricing group does not belong to this property.');
            return redirect()->route('pricing-groups.index', $this->propertyId);
        }
        
        // Load group data
        $this->group_name = $this->group->group_name;
        $this->room_type = $this->group->room_type;
        $this->base_price = $this->group->base_price;
        $this->description = $this->group->description;
        $this->amenities = $this->group->amenities ?? [];
        $this->status = $this->group->status;
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $this->group->group_name = $this->group_name;
            $this->group->room_type = $this->room_type;
            $this->group->base_price = $this->base_price;
            $this->group->description = $this->description;
            $this->group->amenities = $this->amenities;
            $this->group->status = $this->status;
            $this->group->save();
            
            // Update all units that use this pricing group
            foreach ($this->group->units as $unit) {
                $unit->applyGroupPricing();
            }
            
            session()->flash('success', 'Pricing group updated successfully!');
            return redirect()->route('pricing-groups.index', $this->propertyId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating pricing group: ' . $e->getMessage());
        }
    }
    
    public function addAmenity()
    {
        $this->amenities[] = '';
    }
    
    public function removeAmenity($index)
    {
        unset($this->amenities[$index]);
        $this->amenities = array_values($this->amenities);
    }
    
    public function render()
    {
        return view('livewire.pricing-groups.pricing-group-edit', [
            'property' => $this->property,
            'group' => $this->group
        ]);
    }
}
