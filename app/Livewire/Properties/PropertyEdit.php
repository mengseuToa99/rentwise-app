<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class PropertyEdit extends Component
{
    use WithFileUploads;
    
    public $propertyId;
    public $property_name;
    public $street_number = '';
    public $house_number = '';
    public $village = '';
    public $commune = '';
    public $district = '';
    public $province = '';
    public $description;
    public $totalFloors;
    public $totalRooms;
    public $status;
    
    protected $rules = [
        'property_name' => 'required|string|max:255',
        'street_number' => 'nullable|string|max:255',
        'house_number' => 'nullable|string|max:255',
        'village' => 'required|string|max:255',
        'commune' => 'required|string|max:255',
        'district' => 'required|string|max:255',
        'province' => 'required|string|max:255',
        'description' => 'required|string',
        'totalFloors' => 'required|integer|min:1',
        'totalRooms' => 'required|integer|min:0',
        'status' => 'required|string|in:active,inactive',
    ];
    
    public function mount($property)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // If $property is just an ID (string), fetch the property
        if (is_string($property) || is_numeric($property)) {
            $this->propertyId = $property;
            $property = Property::findOrFail($property);
        } else {
            $this->propertyId = $property->property_id;
        }
        
        // Now $property is definitely a Property model
        $this->property_name = $property->property_name;
        
        // Parse the address into individual components
        $this->parseAddressComponents($property->address);
        
        $this->description = $property->description;
        $this->totalFloors = $property->total_floors;
        $this->totalRooms = $property->total_rooms;
        $this->status = $property->status;
    }
    
    /**
     * Parse address string into components
     */
    private function parseAddressComponents($address)
    {
        // Default empty values
        $this->street_number = '';
        $this->house_number = '';
        $this->village = '';
        $this->commune = '';
        $this->district = '';
        $this->province = '';
        
        if (empty($address)) {
            return;
        }
        
        // Try to parse the address based on commas
        $parts = array_map('trim', explode(',', $address));
        
        // Assign parts to corresponding fields based on position and availability
        $count = count($parts);
        
        if ($count >= 6) {
            // If we have all 6 components
            $this->street_number = $parts[0];
            $this->house_number = $parts[1];
            $this->village = $parts[2];
            $this->commune = $parts[3];
            $this->district = $parts[4];
            $this->province = $parts[5];
        } elseif ($count == 5) {
            // If we have 5 components, assume street or house number is missing
            $this->house_number = $parts[0];
            $this->village = $parts[1];
            $this->commune = $parts[2];
            $this->district = $parts[3];
            $this->province = $parts[4];
        } elseif ($count == 4) {
            // If we have 4 components, assume street and house number are missing
            $this->village = $parts[0];
            $this->commune = $parts[1];
            $this->district = $parts[2];
            $this->province = $parts[3];
        } elseif ($count == 3) {
            // If we have 3 components, assume village is the first
            $this->village = $parts[0];
            $this->commune = $parts[1];
            $this->district = $parts[2];
        } elseif ($count == 2) {
            // If only 2 components, assume district and province
            $this->district = $parts[0];
            $this->province = $parts[1];
        } elseif ($count == 1) {
            // If only one component, assume it's the province
            $this->province = $parts[0];
        }
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $property = Property::findOrFail($this->propertyId);
            
            // Build the full address from individual components
            $fullAddress = trim(implode(', ', array_filter([
                $this->street_number,
                $this->house_number,
                $this->village,
                $this->commune,
                $this->district,
                $this->province
            ])));
            
            $property->property_name = $this->property_name;
            $property->address = $fullAddress;
            $property->description = $this->description;
            $property->total_floors = $this->totalFloors;
            $property->total_rooms = $this->totalRooms;
            $property->status = $this->status;
            
            $property->save();
            
            session()->flash('success', 'Property updated successfully.');
            return redirect()->route('properties.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update property: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.properties.property-edit');
    }
} 