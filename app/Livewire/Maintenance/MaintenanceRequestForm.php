<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MaintenanceRequest;
use App\Models\MaintenancePhoto;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class MaintenanceRequestForm extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public $mode = 'create';
    public $requestId;
    
    // Form fields
    public $title;
    public $description;
    public $priority = 'medium';
    public $status = 'pending';
    public $landlord_notes;
    public $photos = [];
    public $existingPhotos;
    
    // For tenants
    public $selectedProperty;
    public $selectedUnit;
    public $properties = [];
    public $units = [];
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,medium,high,urgent',
        'status' => 'required|in:pending,in_progress,completed,rejected',
        'landlord_notes' => 'nullable|string',
        'selectedProperty' => 'required|exists:property_details,property_id',
        'selectedUnit' => 'required|exists:room_details,room_id',
        'photos.*' => 'nullable|image|max:5120' // 5MB max per photo
    ];

    public function mount($request_id = null)
    {
        $user = Auth::user();
        $this->isLandlord = $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        });

        // Initialize photos array
        $this->photos = [];
        $this->existingPhotos = collect();

        // Set mode based on route name
        $routeName = request()->route()->getName();
        
        // Handle create mode
        if ($routeName === 'maintenance.create') {
            $this->mode = 'create';
            if ($this->isLandlord) {
                abort(403, 'Landlords cannot create maintenance requests.');
            }
            // Load properties for tenant
            $this->loadProperties();
            return;
        }
        
        // For show/edit modes, we need a request ID
        if (!$request_id) {
            abort(404, 'Maintenance request not found.');
        }
        
        $this->requestId = $request_id;
        $this->loadMaintenanceRequest();
        
        // Set mode based on route and permissions
        if ($routeName === 'maintenance.edit') {
            $this->mode = 'edit';
            // Only tenants can edit their pending requests
            if ($this->isLandlord || $this->status !== 'pending') {
                abort(403, 'You cannot edit this maintenance request.');
            }
        } else {
            $this->mode = 'show';
        }
        
        $this->loadProperties();
    }
    
    public function loadMaintenanceRequest()
    {
        $request = MaintenanceRequest::with(['photos', 'property', 'room', 'tenant'])->findOrFail($this->requestId);
        
        // Check if user has permission to view this request
        $this->authorize('view', $request);
        
        $this->title = $request->title;
        $this->description = $request->description;
        $this->priority = $request->priority;
        $this->status = $request->status;
        $this->landlord_notes = $request->landlord_notes;
        $this->selectedProperty = $request->property_id;
        $this->selectedUnit = $request->room_id;
        $this->existingPhotos = $request->photos;
        
        $this->loadUnits();
    }
    
    public function loadProperties()
    {
        $user = Auth::user();
        
        if ($user->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'tenant';
        })) {
            // For tenants, get properties from their active rental agreements
            $this->properties = Property::select([
                'property_details.property_id',
                'property_details.property_name',
                'property_details.house_building_number',
                'property_details.street',
                'property_details.village',
                'property_details.commune',
                'property_details.district',
                'property_details.total_floors',
                'property_details.total_rooms',
                'property_details.description',
                'property_details.status',
                'property_details.landlord_id'
            ])
                ->join('room_details', 'property_details.property_id', '=', 'room_details.property_id')
                ->join('rental_details', 'room_details.room_id', '=', 'rental_details.room_id')
                ->where('rental_details.tenant_id', $user->user_id)
                ->where(function($query) {
                    $query->where('rental_details.end_date', '>', now())
                          ->orWhereNull('rental_details.end_date');
                })
                ->distinct()
                ->get();
        } else {
            // For landlords, show all their properties
            $this->properties = Property::where('landlord_id', $user->user_id)->get();
        }
    }
    
    public function updatedSelectedProperty($value)
    {
        $this->selectedUnit = '';
        $this->loadUnits();
    }
    
    public function loadUnits()
    {
        if (!$this->selectedProperty) {
            $this->units = [];
            return;
        }
        
        $user = Auth::user();
        
        if ($user->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'tenant';
        })) {
            // For tenants, get units from their active rental agreements
            $this->units = Unit::select([
                'room_details.room_id',
                'room_details.property_id',
                'room_details.room_number',
                'room_details.floor_number',
                'room_details.room_name',
                'room_details.room_type',
                'room_details.description',
                'room_details.available',
                'room_details.rent_amount',
                'room_details.due_date'
            ])
                ->join('rental_details', 'room_details.room_id', '=', 'rental_details.room_id')
                ->where('rental_details.tenant_id', $user->user_id)
                ->where('room_details.property_id', $this->selectedProperty)
                ->where(function($query) {
                    $query->where('rental_details.end_date', '>', now())
                          ->orWhereNull('rental_details.end_date');
                })
                ->get();
        } else {
            // For landlords, show all units in the property
            $this->units = Unit::where('property_id', $this->selectedProperty)->get();
        }
    }
    
    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:5120'
        ]);
    }
    
    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }
    
    public function deleteExistingPhoto($photoId)
    {
        $photo = MaintenancePhoto::findOrFail($photoId);
        
        if ($photo->request_id === $this->requestId) {
            if (Storage::exists($photo->photo_path)) {
            Storage::delete($photo->photo_path);
            }
            $photo->delete();
            
            $this->existingPhotos = $this->existingPhotos->filter(function($p) use ($photoId) {
                return $p->photo_id !== $photoId;
            });
        }
    }
    
    public function save()
    {
        $this->validate();
        
        $user = Auth::user();

        if ($this->mode === 'create') {
            // Only tenants can create requests
            $this->authorize('create', MaintenanceRequest::class);

            $request = MaintenanceRequest::create([
                'tenant_id' => $user->user_id,
                'property_id' => $this->selectedProperty,
                'room_id' => $this->selectedUnit,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => 'pending', // Always start as pending
            ]);
            
            // Handle photo uploads
            if (!empty($this->photos)) {
                foreach ($this->photos as $photo) {
                    $path = $photo->store('maintenance-photos', 'public');
                    MaintenancePhoto::create([
                        'request_id' => $request->request_id,
                        'photo_path' => $path,
                        'photo_type' => 'before',
                        'uploaded_by_type' => 'tenant',
                        'uploaded_by_id' => $user->user_id,
                    ]);
                }
            }
            
            session()->flash('success', 'Maintenance request created successfully.');
        } else {
            $request = MaintenanceRequest::findOrFail($this->requestId);
            
            // Check if user can update this request
            $this->authorize('update', $request);

            // For landlords, only allow status and notes updates
            if ($user->roles->contains(function($role) {
                return strtolower($role->role_name) === 'landlord';
            })) {
                $request->update([
                    'status' => $this->status,
                    'landlord_notes' => $this->landlord_notes,
                    'completed_at' => $this->status === 'completed' ? now() : null,
                ]);
                
                // Handle photo uploads for landlords (after photos)
                if (!empty($this->photos)) {
                    foreach ($this->photos as $photo) {
                        $path = $photo->store('maintenance-photos', 'public');
                        MaintenancePhoto::create([
                            'request_id' => $request->request_id,
                            'photo_path' => $path,
                            'photo_type' => 'after',
                            'uploaded_by_type' => 'landlord',
                            'uploaded_by_id' => $user->user_id,
                        ]);
                    }
                }
            } else {
                $request->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'priority' => $this->priority,
                ]);
                
                // Handle photo uploads for tenants (before photos)
                if (!empty($this->photos)) {
                    foreach ($this->photos as $photo) {
                        $path = $photo->store('maintenance-photos', 'public');
                        MaintenancePhoto::create([
                            'request_id' => $request->request_id,
                            'photo_path' => $path,
                            'photo_type' => 'before',
                            'uploaded_by_type' => 'tenant',
                            'uploaded_by_id' => $user->user_id,
                        ]);
                    }
                }
            }
            
            session()->flash('success', 'Maintenance request updated successfully.');
        }
        
        return redirect()->route('maintenance.index');
    }
    
    public function render()
    {
        $user = Auth::user();
        $isLandlord = $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        });

        return view('livewire.maintenance.maintenance-request-form', [
            'priorities' => [
                'low' => 'Low',
                'medium' => 'Medium',
                'high' => 'High',
                'urgent' => 'Urgent'
            ],
            'statuses' => [
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'rejected' => 'Rejected'
            ],
            'isLandlord' => $isLandlord
        ]);
    }
} 