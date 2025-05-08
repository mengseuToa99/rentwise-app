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
    public $address;
    public $description;
    public $totalFloors;
    public $totalRooms;
    public $location;
    public $status;
    
    protected $rules = [
        'property_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'description' => 'required|string',
        'totalFloors' => 'required|integer|min:1',
        'totalRooms' => 'required|integer|min:0',
        'location' => 'required|string|max:255',
        'status' => 'required|string|in:active,inactive',
    ];
    
    public function mount($property)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->propertyId = $property;
        
        $property = Property::find($this->propertyId);
        
        if (!$property) {
            session()->flash('error', 'Property not found');
            return redirect()->route('properties.index');
        }
        
        // Check if user is authorized to edit this property
        $authUser = Auth::user();
        
        if (!$authUser) {
            session()->flash('error', 'User profile not found');
            return redirect()->route('properties.index');
        }
        
        $userRoles = $authUser->roles ?? collect([]);
        // if (!$userRoles->contains('role_name', 'admin') && $property->landlord_id !== $authUser->user_id) {
        //     abort(403, 'Unauthorized action.');
        // }
        
        $this->property_name = $property->property_name;
        $this->address = $property->address;
        $this->description = $property->description;
        $this->totalFloors = $property->total_floors;
        $this->totalRooms = $property->total_rooms;
        $this->location = $property->location;
        $this->status = $property->status;
    }
    
    public function update()
    {
        $this->validate();
        
        try {
            $property = Property::find($this->propertyId);
            
            if (!$property) {
                session()->flash('error', 'Property not found');
                return redirect()->route('properties.index');
            }
            
            $property->property_name = $this->property_name;
            $property->address = $this->address;
            $property->description = $this->description;
            $property->total_floors = $this->totalFloors;
            $property->total_rooms = $this->totalRooms;
            $property->location = $this->location;
            $property->status = $this->status;
            $property->save();
            
            session()->flash('success', 'Property updated successfully!');
            return redirect()->route('properties.show', $property->property_id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating property: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.properties.property-edit');
    }
} 