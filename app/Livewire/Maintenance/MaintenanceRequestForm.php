<?php

namespace App\Livewire\Maintenance;

use Livewire\Component;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MaintenanceRequestForm extends Component
{
    use AuthorizesRequests;

    public $mode = 'create';
    public $requestId;
    
    // Form fields
    public $title;
    public $description;
    public $priority = 'medium';
    public $status = 'pending';
    public $landlord_notes;
    
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
    ];

    public function mount($requestId = null)
    {
        $user = Auth::user();

        // Check if user is a landlord trying to create a request
        if ($this->mode === 'create' && $user->roles->contains(function($role) {
            return strtolower($role->role_name) === 'landlord';
        })) {
            abort(403, 'Landlords cannot create maintenance requests.');
        }

        if ($requestId) {
            $this->mode = 'edit';
            $this->requestId = $requestId;
            $this->loadMaintenanceRequest();
        }
        
        $this->loadProperties();
    }
    
    public function loadMaintenanceRequest()
    {
        $request = MaintenanceRequest::findOrFail($this->requestId);
        
        // Check if user has permission to view this request
        $this->authorize('view', $request);
        
        $this->title = $request->title;
        $this->description = $request->description;
        $this->priority = $request->priority;
        $this->status = $request->status;
        $this->landlord_notes = $request->landlord_notes;
        $this->selectedProperty = $request->property_id;
        $this->selectedUnit = $request->room_id;
        
        $this->loadUnits();
    }
    
    public function loadProperties()
    {
        $user = Auth::user();
        
        if ($user->roles->contains(function($role) { 
            return strtolower($role->role_name) === 'tenant';
        })) {
            // For tenants, only show their rented property
            $this->properties = Property::whereHas('units.rentals', function($query) use ($user) {
                $query->where('tenant_id', $user->user_id)
                      ->where('status', 'active');
            })->get();
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
            // For tenants, only show their rented unit
            $this->units = Unit::whereHas('rentals', function($query) use ($user) {
                $query->where('tenant_id', $user->user_id)
                      ->where('status', 'active');
            })->where('property_id', $this->selectedProperty)->get();
        } else {
            // For landlords, show all units in the property
            $this->units = Unit::where('property_id', $this->selectedProperty)->get();
        }
    }
    
    public function save()
    {
        $this->validate();
        
        $user = Auth::user();

        if ($this->mode === 'create') {
            // Only tenants can create requests
            $this->authorize('create', MaintenanceRequest::class);

            MaintenanceRequest::create([
                'tenant_id' => $user->user_id,
                'property_id' => $this->selectedProperty,
                'room_id' => $this->selectedUnit,
                'title' => $this->title,
                'description' => $this->description,
                'priority' => $this->priority,
                'status' => 'pending', // Always start as pending
            ]);
            
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
            } else {
                $request->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'priority' => $this->priority,
                    'status' => $this->status,
                    'landlord_notes' => $this->landlord_notes,
                    'completed_at' => $this->status === 'completed' ? now() : null,
                ]);
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