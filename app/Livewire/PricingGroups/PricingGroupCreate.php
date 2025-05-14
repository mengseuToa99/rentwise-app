<?php

namespace App\Livewire\PricingGroups;

use Livewire\Component;
use App\Models\Property;
use App\Models\PricingGroup;
use Illuminate\Support\Facades\Auth;

class PricingGroupCreate extends Component
{
    public $property;
    public $propertyId;
    
    public $group_name;
    public $room_type;
    public $base_price;
    public $description;
    public $amenities = [];
    public $status = 'active';
    
    protected $rules = [
        'group_name' => 'required|string|max:255',
        'room_type' => 'required|string|max:255',
        'base_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'amenities' => 'nullable|array',
        'status' => 'required|in:active,inactive'
    ];
    
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
            session()->flash('error', 'You do not have permission to add pricing groups to this property.');
            return redirect()->route('properties.index');
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            $pricingGroup = new PricingGroup();
            $pricingGroup->property_id = $this->propertyId;
            $pricingGroup->group_name = $this->group_name;
            $pricingGroup->room_type = $this->room_type;
            $pricingGroup->base_price = $this->base_price;
            $pricingGroup->description = $this->description;
            $pricingGroup->amenities = $this->amenities;
            $pricingGroup->status = $this->status;
            $pricingGroup->save();
            
            session()->flash('success', 'Pricing group created successfully!');
            return redirect()->route('pricing-groups.index', $this->propertyId);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating pricing group: ' . $e->getMessage());
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
        return view('livewire.pricing-groups.pricing-group-create', [
            'property' => $this->property
        ]);
    }
}
