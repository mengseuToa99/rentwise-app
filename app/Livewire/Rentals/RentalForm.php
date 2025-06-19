<?php

namespace App\Livewire\Rentals;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalForm extends Component
{
    use WithFileUploads;

    public $rentalId;
    public $mode = 'create';
    
    // Form fields
    public $tenant_id;
    public $property_id;
    public $room_id;
    public $start_date;
    public $end_date;
    public $lease_agreement;
    public $existing_lease_agreement;
    
    // Tenant search
    public $tenantSearch = '';
    public $searchResults = [];
    public $selectedTenant = null;
    public $showNewTenantForm = false;
    
    // New tenant fields
    public $newTenant = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone_number' => '',
    ];
    
    // For dropdown options
    public $properties = [];
    public $units = [];
    
    protected $rules = [
        'tenant_id' => 'required|exists:users,user_id',
        'room_id' => 'required|exists:room_details,room_id',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'lease_agreement' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
    ];
    
    protected $messages = [
        'tenant_id.required' => 'Please select a tenant',
        'room_id.required' => 'Please select a unit',
        'start_date.required' => 'Please select a start date',
        'end_date.after_or_equal' => 'End date must be after or equal to start date',
    ];
    
    public function mount($rentalId = null)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            $authUser = Auth::user();
            
            if (!$authUser) {
                session()->flash('error', 'User profile not found');
                return redirect()->route('rentals.index');
            }
            
            $userRoles = $authUser->roles ?? collect([]);
            
            // Load properties based on user role
            if ($userRoles->contains('role_name', 'admin')) {
                $this->properties = Property::pluck('property_name', 'property_id');
            } else {
                $this->properties = Property::where('landlord_id', $authUser->user_id)
                                            ->pluck('property_name', 'property_id');
            }
            
            // Set default start and end dates
            $this->start_date = now()->format('Y-m-d');
            $this->end_date = now()->addYear()->format('Y-m-d');
            
            // If editing an existing rental
            if ($rentalId) {
                $this->rentalId = $rentalId;
                $this->mode = 'edit';
                
                $rental = Rental::findOrFail($rentalId);
                
                // Authorization check
                if (!$userRoles->contains('role_name', 'admin') && $rental->landlord_id !== $authUser->user_id) {
                    session()->flash('error', 'You are not authorized to edit this rental');
                    return redirect()->route('rentals.index');
                }
                
                // Load unit and property data
                $unit = Unit::findOrFail($rental->room_id);
                $this->property_id = $unit->property_id;
                $this->loadUnits();
                
                // Populate form fields
                $this->tenant_id = $rental->tenant_id;
                $this->room_id = $rental->room_id;
                $this->start_date = Carbon::parse($rental->start_date)->format('Y-m-d');
                $this->end_date = $rental->end_date ? Carbon::parse($rental->end_date)->format('Y-m-d') : null;
                $this->existing_lease_agreement = $rental->lease_agreement;
                
                // Load selected tenant data
                $this->selectedTenant = User::find($this->tenant_id);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading rental form: ' . $e->getMessage());
            return redirect()->route('rentals.index');
        }
    }
    
    public function updatedTenantSearch()
    {
        if (strlen($this->tenantSearch) >= 2) {
            $this->searchResults = User::whereHas('roles', function($query) {
                    $query->where('role_name', 'tenant');
                })
                ->where(function($query) {
                    $query->where('first_name', 'like', '%' . $this->tenantSearch . '%')
                        ->orWhere('last_name', 'like', '%' . $this->tenantSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->tenantSearch . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->tenantSearch . '%');
                })
                ->select('user_id', 'first_name', 'last_name', 'email', 'phone_number')
                ->limit(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }
    
    public function selectTenant($userId)
    {
        $this->tenant_id = $userId;
        $this->selectedTenant = User::find($userId);
        $this->searchResults = [];
        $this->tenantSearch = '';
    }
    
    public function toggleNewTenantForm()
    {
        $this->showNewTenantForm = !$this->showNewTenantForm;
        $this->resetNewTenantFields();
    }
    
    public function resetNewTenantFields()
    {
        $this->newTenant = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone_number' => '',
        ];
    }
    
    public function createAndSelectTenant()
    {
        $this->validate([
            'newTenant.first_name' => 'required|string|max:255',
            'newTenant.last_name' => 'required|string|max:255',
            'newTenant.email' => 'required|email|unique:users,email',
            'newTenant.phone_number' => 'required|string|max:20',
        ], [
            'newTenant.first_name.required' => 'First name is required',
            'newTenant.last_name.required' => 'Last name is required',
            'newTenant.email.required' => 'Email is required',
            'newTenant.email.unique' => 'Email already exists',
            'newTenant.phone_number.required' => 'Phone number is required',
        ]);
        
        try {
            // Create new tenant user
            $user = new User();
            $user->first_name = $this->newTenant['first_name'];
            $user->last_name = $this->newTenant['last_name'];
            $user->email = $this->newTenant['email'];
            $user->phone_number = $this->newTenant['phone_number'];
            $user->username = strtolower($this->newTenant['first_name'] . '.' . $this->newTenant['last_name']);
            
            // Generate a random password
            $password = substr(md5(rand()), 0, 8);
            $user->password_hash = bcrypt($password);
            $user->status = 'active';
            $user->save();
            
            // Assign tenant role
            $tenantRole = DB::table('roles')->where('role_name', 'tenant')->first();
            if ($tenantRole) {
                DB::table('user_roles')->insert([
                    'user_id' => $user->user_id,
                    'role_id' => $tenantRole->role_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Select the newly created tenant
            $this->selectTenant($user->user_id);
            $this->showNewTenantForm = false;
            
            session()->flash('tenant_success', "New tenant created successfully. Their temporary password is: {$password}");
        } catch (\Exception $e) {
            session()->flash('tenant_error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }
    
    public function updatedPropertyId()
    {
        $this->room_id = null;
        $this->loadUnits();
    }
    
    public function loadUnits()
    {
        try {
            if ($this->property_id) {
                // When editing, include the currently assigned unit even if it's not available
                if ($this->mode === 'edit' && $this->room_id) {
                    $this->units = Unit::where('property_id', $this->property_id)
                                      ->where(function($query) {
                                          $query->where('available', true)
                                                ->orWhere('room_id', $this->room_id);
                                      })
                                      ->pluck('room_number', 'room_id');
                } else {
                    // Only show available units when creating
                    $this->units = Unit::where('property_id', $this->property_id)
                                      ->where('available', true)
                                      ->pluck('room_number', 'room_id');
                }
            } else {
                $this->units = [];
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading units: ' . $e->getMessage());
        }
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            $authUser = Auth::user();
            
            if (!$authUser) {
                session()->flash('error', 'User profile not found');
                return;
            }
            
            $leaseAgreementPath = $this->existing_lease_agreement;
            
            // Handle lease agreement file upload
            if ($this->lease_agreement) {
                // Delete existing file if being replaced
                if ($leaseAgreementPath && Storage::disk('public')->exists($leaseAgreementPath)) {
                    Storage::disk('public')->delete($leaseAgreementPath);
                }
                
                $leaseAgreementPath = $this->lease_agreement->store('lease_agreements', 'public');
            }
            
            $rentalData = [
                'landlord_id' => $authUser->user_id,
                'tenant_id' => $this->tenant_id,
                'room_id' => $this->room_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'lease_agreement' => $leaseAgreementPath,
                'status' => 'active', // Set default status to active
            ];
            
            if ($this->mode === 'edit') {
                $rental = Rental::findOrFail($this->rentalId);
                
                // Handle unit availability if changing units
                if ($rental->room_id != $this->room_id) {
                    // Make old unit available again
                    $oldUnit = Unit::find($rental->room_id);
                    if ($oldUnit) {
                        $oldUnit->available = true;
                        $oldUnit->status = 'vacant';
                        $oldUnit->save();
                    }
                    
                    // Make new unit unavailable
                    $newUnit = Unit::find($this->room_id);
                    if ($newUnit) {
                        $newUnit->available = false;
                        $newUnit->status = 'occupied';
                        $newUnit->save();
                    }
                }
                
                $rental->update($rentalData);
                session()->flash('success', 'Rental updated successfully');
            } else {
                Rental::create($rentalData);
                
                // Make the unit unavailable and occupied
                $unit = Unit::find($this->room_id);
                if ($unit) {
                    $unit->available = false;
                    $unit->status = 'occupied';
                    $unit->save();
                }
                
                session()->flash('success', 'Rental created successfully');
            }
            
            return redirect()->route('rentals.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save rental: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.rentals.rental-form');
    }
} 