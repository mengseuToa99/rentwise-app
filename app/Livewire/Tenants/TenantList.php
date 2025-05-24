<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $propertyFilter = '';
    public $statusFilter = '';
    public $perPage = 10; // Default number of tenants per page
    
    protected $queryString = ['search', 'propertyFilter', 'statusFilter', 'perPage'];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }
    
    public function updatedPerPage()
    {
        $this->resetPage(); // Reset pagination when changing items per page
    }
    
    public function render()
    {
        $user = Auth::user();
        
        // Base query depends on user role
        if ($user->hasRole('admin')) {
            // Admins see all tenants
            $query = User::query()
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
                ->where('roles.role_name', 'tenant')
                ->select('users.*')
                ->distinct();
                
                // Add rental information via left join
                $query->leftJoin('rental_details', 'users.user_id', '=', 'rental_details.tenant_id')
                      ->leftJoin('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
                      ->leftJoin('property_details', 'room_details.property_id', '=', 'property_details.property_id')
                      ->addSelect(
                          DB::raw('rental_details.rental_id'),
                          DB::raw('rental_details.start_date'),
                          DB::raw('rental_details.end_date'),
                          DB::raw('rental_details.status as rental_status'),
                          DB::raw('room_details.room_number'),
                          DB::raw('property_details.property_id'),
                          DB::raw('property_details.property_name')
                      );
        } else if ($user->hasRole('landlord')) {
            // Landlords see only tenants who rent from them
            // This query gets tenants that have rental records with this landlord
            $query = User::query()
                ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
                ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
                ->join('rental_details', 'users.user_id', '=', 'rental_details.tenant_id')
                ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
                ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
                ->where('roles.role_name', 'tenant')
                ->where('rental_details.landlord_id', $user->user_id)
                ->select(
                    'users.*',
                    'rental_details.rental_id',
                    'rental_details.start_date',
                    'rental_details.end_date',
                    'rental_details.status as rental_status',
                    'room_details.room_number',
                    'property_details.property_id',
                    'property_details.property_name'
                )
                ->distinct();
        } else {
            // Default case - return empty collection
            $query = User::where('user_id', 0); // No results
        }
        
        // Apply property filter
        if (!empty($this->propertyFilter)) {
            $query->where('property_details.property_id', $this->propertyFilter);
        }
        
        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('rental_details.status', $this->statusFilter);
        }
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('users.first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('users.email', 'like', '%' . $this->search . '%')
                  ->orWhere('users.phone_number', 'like', '%' . $this->search . '%')
                  ->orWhere('property_details.property_name', 'like', '%' . $this->search . '%')
                  ->orWhere('room_details.room_number', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get tenants with pagination
        $tenants = $this->perPage === 'all' 
            ? $query->get() 
            : $query->paginate($this->perPage);
        
        // Get properties for filter dropdown - only from this landlord
        $properties = \App\Models\Property::where('landlord_id', $user->user_id)
                             ->pluck('property_name', 'property_id');
        
        // Get statuses for filter dropdown
        $statuses = [
            'active' => 'Active',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'pending' => 'Pending'
        ];
        
        // Create array of pagination options
        $paginationOptions = [
            10 => '10 per page',
            25 => '25 per page',
            50 => '50 per page',
            100 => '100 per page',
            'all' => 'Show All'
        ];
        
        return view('livewire.tenants.tenant-list', [
            'tenants' => $tenants,
            'properties' => $properties,
            'statuses' => $statuses,
            'paginationOptions' => $paginationOptions
        ]);
    }
    
    public function viewTenantDetails($tenantId)
    {
        return redirect()->route('tenants.show', ['tenant' => $tenantId]);
    }
} 