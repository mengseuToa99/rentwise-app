<?php

namespace App\Livewire\Rentals;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rental;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentalList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $propertyFilter = '';
    public $statusFilter = '';
    public $perPage = 10; // Default number of rentals per page
    
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
        $query = Rental::query();
        
        // Join with related tables for better filtering
        $query->join('users', 'rental_details.tenant_id', '=', 'users.user_id')
              ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
              ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
              ->select(
                  'rental_details.*', 
                  DB::raw("CONCAT(users.first_name, ' ', users.last_name) as tenant_name"), 
                  'property_details.property_name', 
                  'room_details.room_number'
              );
        
        // Always show only rentals related to this landlord
        $query->where('rental_details.landlord_id', $user->user_id);
        
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
                  ->orWhere('room_details.room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('property_details.property_name', 'like', '%' . $this->search . '%');
            });
        }
        
        // Get rentals with relationships
        $rentals = $this->perPage === 'all' 
            ? $query->get() 
            : $query->paginate($this->perPage);
        
        // Get properties for filter dropdown - only from this landlord
        $properties = \App\Models\Property::where('landlord_id', $user->user_id)
                             ->select('property_id', 'property_name')
                             ->get()
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
        
        return view('livewire.rentals.rental-list', [
            'rentals' => $rentals,
            'properties' => $properties,
            'statuses' => $statuses,
            'paginationOptions' => $paginationOptions
        ]);
    }
    
    public function deleteRental($rentalId)
    {
        try {
            $rental = Rental::find($rentalId);
            
            if (!$rental) {
                session()->flash('error', 'Rental not found');
                return;
            }
            
            // Verify authorization
            $user = Auth::user();
            if ($rental->landlord_id !== $user->user_id) {
                session()->flash('error', 'You are not authorized to delete this rental');
                return;
            }
            
            // Make the room available again
            $unit = Unit::find($rental->room_id);
            if ($unit) {
                $unit->available = true;
                $unit->save();
            }
            
            $rental->delete();
            
            session()->flash('success', 'Rental deleted successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete rental: ' . $e->getMessage());
        }
    }
} 