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
        'propertyId' => 'required|integer|exists:property_details,property_id',
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
        
        // Get property ID from query parameter if available and cast it to integer
        $propertyId = request()->query('property');
        if ($propertyId !== null && is_numeric($propertyId)) {
            $this->propertyId = (int)$propertyId;
            \Log::info('Property ID set from query:', ['id' => $this->propertyId, 'type' => gettype($this->propertyId)]);
        }
        
        // Make sure the user has permission for this property
        if (!empty($this->propertyId)) {
            $authUser = Auth::user();
            
            if (!$authUser) {
                session()->flash('error', 'User profile not found');
                return redirect()->route('properties.index');
            }
            
            $property = Property::find($this->propertyId);
            \Log::info('Found property:', ['property' => $property ? $property->toArray() : null]);
            
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
        \Log::info('Property ID updated:', ['raw_value' => $value, 'type' => gettype($value)]);
        
        // Cast the property ID to integer if it's not null and is numeric
        if ($value !== null && $value !== '' && is_numeric($value)) {
            $this->propertyId = (int)$value;
            \Log::info('Property ID cast to int:', ['cast_value' => $this->propertyId, 'type' => gettype($this->propertyId)]);
        } else {
            $this->propertyId = null;
            \Log::info('Property ID set to null');
        }
        
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
        \Log::info('Starting unit creation with data:', [
            'propertyId' => $this->propertyId,
            'type' => gettype($this->propertyId),
            'roomName' => $this->roomName,
            'roomNumber' => $this->roomNumber
        ]);
        
        // Ensure propertyId is an integer
        if (!is_numeric($this->propertyId)) {
            session()->flash('error', 'Invalid property ID');
            return;
        }
        
        $this->propertyId = (int)$this->propertyId;
        
        try {
            $this->validate();
            
            // Verify property exists
            $property = Property::find($this->propertyId);
            if (!$property) {
                throw new \Exception('Selected property does not exist');
            }
            
            // Parse and format the date properly
            $dueDate = \Carbon\Carbon::parse($this->dueDate)->format('Y-m-d');
            
            $unit = new Unit();
            $unit->property_id = $this->propertyId;
            $unit->pricing_group_id = $this->pricing_group_id;
            $unit->room_name = $this->roomName;
            $unit->room_number = $this->roomNumber;
            $unit->floor_number = $this->floorNumber;
            $unit->room_type = $this->type;
            $unit->description = $this->description;
            $unit->rent_amount = $this->rentAmount;
            $unit->due_date = $dueDate;
            $unit->available = $this->available;
            $unit->status = 'vacant';
            
            // Debug information before save
            \Log::info('Unit data before save:', array_merge(
                $unit->toArray(),
                ['property_id_type' => gettype($unit->property_id)]
            ));
            
            $unit->save();
            
            // Debug information after save
            \Log::info('Unit saved successfully:', [
                'unit_id' => $unit->room_id,
                'property_id' => $unit->property_id,
                'property_id_type' => gettype($unit->property_id)
            ]);
            
            session()->flash('success', 'Unit created successfully!');
            
            if (!empty($this->propertyId)) {
                return redirect()->route('properties.show', $this->propertyId);
            } else {
                return redirect()->route('units.index');
            }
            
        } catch (\Exception $e) {
            // More detailed error logging
            \Log::error('Error creating unit:', [
                'message' => $e->getMessage(),
                'property_id' => $this->propertyId,
                'property_id_type' => gettype($this->propertyId),
                'trace' => $e->getTraceAsString()
            ]);
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
        
        \Log::info('Properties from database:', [
            'properties' => $properties->map(function($prop) {
                return [
                    'id' => $prop->property_id,
                    'name' => $prop->property_name
                ];
            })->toArray()
        ]);
        
        return view('livewire.units.unit-create', [
            'properties' => $properties
        ]);
    }
} 