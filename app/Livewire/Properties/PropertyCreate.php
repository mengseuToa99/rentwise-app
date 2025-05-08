<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use App\Models\Property;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class PropertyCreate extends Component
{
    use WithFileUploads;
    
    public $property_name;
    public $address;
    public $description;
    public $totalFloors = 1;
    public $totalRooms = 0;
    public $location = '';
    
    protected $rules = [
        'property_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'description' => 'required|string',
        'totalFloors' => 'required|integer|min:1',
        'totalRooms' => 'required|integer|min:0',
        'location' => 'required|string|max:255',
    ];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            // Get the authenticated user's ID
            $authUser = Auth::user();
            
            // Find the associated user detail
            $userDetail = UserDetail::where('email', $authUser->email)->first();
            
            if (!$userDetail) {
                session()->flash('error', 'Your user profile is incomplete. Please complete your profile first.');
                return;
            }
            
            $property = new Property();
            $property->property_name = $this->property_name;
            $property->address = $this->address;
            $property->description = $this->description;
            $property->location = $this->location;
            $property->landlord_id = $userDetail->user_id;
            $property->status = 'active';
            $property->total_floors = $this->totalFloors;
            $property->total_rooms = $this->totalRooms;
            $property->save();
            
            session()->flash('success', 'Property created successfully!');
            return redirect()->route('properties.show', $property->property_id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating property: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.properties.property-create');
    }
} 