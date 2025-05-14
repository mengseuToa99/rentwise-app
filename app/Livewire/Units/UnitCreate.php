<?php

namespace App\Livewire\Units;

use Livewire\Component;
use App\Models\Unit;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\PricingGroup;

class UnitCreate extends Component
{
    public $properties = [];
    public $propertyId;
    public $roomName;
    public $roomNumber;
    public $floorNumber = 1;
    public $type;
    public $size;
    public $rentAmount;
    public $dueDate;
    public $available = true;
    public $description = '';
    public $pricing_groups = [];
    public $pricing_group_id;
    
    protected $rules = [
        'propertyId' => 'required|exists:property_details,property_id',
        'roomName' => 'required|string|max:255',
        'roomNumber' => 'required|string|max:20',
        'floorNumber' => 'required|integer|min:1',
        'type' => 'required|string|max:50',
        'rentAmount' => 'required|numeric|min:0',
        'dueDate' => 'required|date',
        'available' => 'boolean',
        'description' => 'nullable|string',
    ];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Set default due date to first day of next month
        $this->dueDate = now()->addMonth()->startOfMonth()->format('Y-m-d');
        
        // Get property ID from query parameter if available
        $this->propertyId = request()->query('property', '');
        
        // Make sure the user has permission for this property
        if (!empty($this->propertyId)) {
            $authUser = Auth::user();
            
            if (!$authUser) {
                session()->flash('error', 'User profile not found');
                return redirect()->route('properties.index');
            }
            
            $property = Property::find($this->propertyId);
            
            $userRoles = $authUser->roles ?? collect([]);
            if (!$property || (!$userRoles->contains('role_name', 'admin') && $property->landlord_id !== $authUser->user_id)) {
                session()->flash('error', 'You do not have permission to add units to this property');
                return redirect()->route('properties.index');
            }
        }
        
        // Load properties for selection
        $this->properties = Property::where('landlord_id', Auth::id())->get();
        
        // If we have a property selected, load pricing groups
        if ($this->propertyId) {
            $this->loadPricingGroups();
        }
    }
    
    public function updatedPropertyId($value)
    {
        // Load the pricing groups when property changes
        $this->loadPricingGroups();
        
        // Reset the pricing group selection
        $this->pricing_group_id = null;
    }
    
    public function loadPricingGroups()
    {
        if ($this->propertyId) {
            $this->pricing_groups = PricingGroup::where('property_id', $this->propertyId)
                ->where('status', 'active')
                ->get();
        } else {
            $this->pricing_groups = [];
        }
    }
    
    public function updatedPricingGroupId($value)
    {
        if ($value) {
            $pricingGroup = PricingGroup::find($value);
            if ($pricingGroup) {
                $this->type = $pricingGroup->room_type;
                $this->rentAmount = $pricingGroup->base_price;
            }
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            $unit = new Unit();
            $unit->property_id = $this->propertyId;
            $unit->pricing_group_id = $this->pricing_group_id;
            $unit->room_name = $this->roomName;
            $unit->room_number = $this->roomNumber;
            $unit->floor_number = $this->floorNumber;
            $unit->room_type = $this->type;
            $unit->description = $this->description;
            $unit->rent_amount = $this->rentAmount;
            $unit->due_date = $this->dueDate;
            $unit->available = $this->available;
            $unit->status = 'vacant';
            $unit->save();
            
            session()->flash('success', 'Unit created successfully!');
            
            if (!empty($this->propertyId)) {
                return redirect()->route('properties.show', $this->propertyId);
            } else {
                return redirect()->route('units.index');
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating unit: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Get properties for the dropdown
        $authUser = Auth::user();
        
        if (!$authUser) {
            return view('livewire.units.unit-create', [
                'properties' => collect([])
            ]);
        }
        
        $query = Property::query();
        
        // If not admin, show only properties owned by this landlord
        $userRoles = $authUser->roles ?? collect([]);
        if (!$userRoles->contains('role_name', 'admin')) {
            $query->where('landlord_id', $authUser->user_id);
        }
        
        $properties = $query->pluck('property_name', 'property_id');
        
        return view('livewire.units.unit-create', [
            'properties' => $properties
        ]);
    }
} 