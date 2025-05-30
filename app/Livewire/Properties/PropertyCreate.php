<?php

namespace App\Livewire\Properties;

use Livewire\Component;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Unit;

class PropertyCreate extends Component
{
    use WithFileUploads;
    
    public $property_name;
    public $street_number = '';
    public $house_number = '';
    public $village = '';
    public $commune = '';
    public $district = '';
    public $province = '';
    public $description;
    public $totalFloors = 1;
    public $totalRooms = 0;
    public $propertyType = 'residential';
    public $yearBuilt;
    public $propertySize;
    public $sizeMeasurement = 'sqft';
    public $amenities = [];
    public $propertyImages = [];
    public $isPetsAllowed = false;
    public $autoGenerateUnits = false;
    public $unitsPerFloor = 4;
    public $unitNamingConvention = 'numeric';
    public $defaultRentAmount = 0;
    public $defaultUnitType = 'Room';
    public $showAutoGeneratePreview = false;
    
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
        'propertyType' => 'required|string|in:residential,commercial,industrial,land',
        'yearBuilt' => 'nullable|integer|min:1800|max:2100',
        'propertySize' => 'nullable|numeric|min:1',
        'sizeMeasurement' => 'required|string|in:sqft,sqm,acre,hectare',
        'amenities' => 'nullable|array',
        'propertyImages.*' => 'nullable|image|max:5120', // 5MB max per image
        'isPetsAllowed' => 'boolean',
        'autoGenerateUnits' => 'boolean',
        'unitsPerFloor' => 'required_if:autoGenerateUnits,true|integer|min:1',
        'unitNamingConvention' => 'required_if:autoGenerateUnits,true|string|in:numeric,letter,ordinal',
        'defaultRentAmount' => 'required_if:autoGenerateUnits,true|numeric|min:0',
        'defaultUnitType' => 'required_if:autoGenerateUnits,true|string|max:50',
    ];

    protected $messages = [
        'propertyImages.*.max' => 'Each image must not exceed 5MB in size.',
        'propertyImages.*.image' => 'Only image files are allowed.',
    ];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Set current year as default
        $this->yearBuilt = date('Y');
    }
    
    public function removeImage($index)
    {
        if (isset($this->propertyImages[$index])) {
            unset($this->propertyImages[$index]);
            $this->propertyImages = array_values($this->propertyImages);
        }
    }
    
    public function create()
    {
        $this->validate();
        
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            if (!$user) {
                session()->flash('error', 'Authentication failed. Please log in again.');
                return redirect()->route('login');
            }
            
            \DB::beginTransaction();
            
            $property = new Property();
            $property->property_name = $this->property_name;
            $property->house_building_number = $this->house_number;
            $property->street = $this->street_number;
            $property->village = $this->village;
            $property->commune = $this->commune;
            $property->district = $this->district;
            $property->description = $this->description;
            $property->landlord_id = $user->user_id;
            $property->status = 'active';
            $property->total_floors = $this->totalFloors;
            $property->total_rooms = $this->totalRooms;
            $property->property_type = $this->propertyType;
            $property->year_built = $this->yearBuilt;
            $property->property_size = $this->propertySize;
            $property->size_measurement = $this->sizeMeasurement;
            $property->amenities = json_encode($this->amenities);
            $property->is_pets_allowed = $this->isPetsAllowed;
            
            $property->save();
            
            // Handle image uploads
            $imageUrls = [];
            if (count($this->propertyImages) > 0) {
                foreach ($this->propertyImages as $image) {
                    $filename = 'property_' . $property->property_id . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('property-images', $filename, 'public');
                    $fullPath = '/storage/' . $path;
                    
                    // Store in property_images table
                    \DB::table('property_images')->insert([
                        'property_id' => $property->property_id,
                        'image_path' => $fullPath,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $imageUrls[] = $fullPath;
                }
                
                // Also store in the images JSON field as backup
                $property->images = json_encode($imageUrls);
                $property->save();
            }
            
            // Generate units if enabled
            if ($this->autoGenerateUnits) {
                $this->generateUnits($property);
            }
            
            \DB::commit();
            
            session()->flash('success', 'Property created successfully!');
            return redirect()->route('properties.show', $property->property_id);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Error creating property: ' . $e->getMessage());
            \Log::error('Property creation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate units for the property based on floors and units per floor
     */
    protected function generateUnits($property)
    {
        for ($floor = 1; $floor <= $this->totalFloors; $floor++) {
            for ($unit = 1; $unit <= $this->unitsPerFloor; $unit++) {
                $unitName = '';
                $roomNumber = '';
                
                // Generate unit name and room number based on naming convention
                if ($this->unitNamingConvention === 'numeric') {
                    $unitName = "Unit " . $floor . str_pad($unit, 2, '0', STR_PAD_LEFT);
                    $roomNumber = $floor . str_pad($unit, 2, '0', STR_PAD_LEFT);
                } elseif ($this->unitNamingConvention === 'letter') {
                    $unitName = "Unit " . $floor . chr(64 + $unit);
                    $roomNumber = $floor . chr(64 + $unit);
                } else {
                    $unitName = $floor . "th Floor Room " . $unit;
                    $roomNumber = $floor . "-" . $unit;
                }
                
                // Create the unit
                $unitModel = new Unit();
                $unitModel->property_id = $property->property_id;
                $unitModel->room_name = $unitName;
                $unitModel->room_number = $roomNumber;
                $unitModel->floor_number = $floor;
                $unitModel->type = $this->defaultUnitType;
                $unitModel->room_type = $this->defaultUnitType;
                $unitModel->rent_amount = $this->defaultRentAmount;
                $unitModel->due_date = now()->format('Y-m-d'); // Default due date
                $unitModel->status = 'vacant';
                $unitModel->description = "Auto-generated unit for " . $property->property_name;
                $unitModel->save();
            }
        }
    }
    
    /**
     * Render the property create view
     */
    public function render()
    {
        $propertyTypes = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'industrial' => 'Industrial',
            'land' => 'Land'
        ];
        
        $sizeUnits = [
            'sqft' => 'sq ft',
            'sqm' => 'sq m',
            'acre' => 'acre',
            'hectare' => 'hectare'
        ];
        
        $availableAmenities = [
            'parking' => 'Parking',
            'swimming_pool' => 'Swimming Pool',
            'gym' => 'Gym',
            'security' => '24/7 Security',
            'elevator' => 'Elevator',
            'internet' => 'Internet',
            'air_conditioning' => 'Air Conditioning',
            'furnished' => 'Furnished'
        ];
        
        return view('livewire.properties.property-create', [
            'propertyTypes' => $propertyTypes,
            'sizeUnits' => $sizeUnits,
            'availableAmenities' => $availableAmenities
        ]);
    }

    public function updatedAutoGenerateUnits()
    {
        // When toggle is clicked, immediately show/hide the preview
        $this->showAutoGeneratePreview = $this->autoGenerateUnits;
    }
    
    public function updatedUnitsPerFloor()
    {
        // When units per floor changes, update the preview if auto-generate is enabled
        if ($this->autoGenerateUnits) {
            $this->showAutoGeneratePreview = true;
        }
    }
    
    public function updatedUnitNamingConvention()
    {
        // When naming convention changes, update the preview if auto-generate is enabled
        if ($this->autoGenerateUnits) {
            $this->showAutoGeneratePreview = true;
        }
    }
    
    public function updatedTotalFloors()
    {
        // When total floors changes, update the preview if auto-generate is enabled
        if ($this->autoGenerateUnits) {
            $this->showAutoGeneratePreview = true;
        }
    }
}