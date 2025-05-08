<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Property;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $stats = [
        'totalProperties' => 0,
        'totalUnits' => 0,
        'occupiedUnits' => 0,
        'vacantUnits' => 0,
        'totalRentals' => 0,
        'activeRentals' => 0,
        'pendingInvoices' => 0,
        'paidInvoices' => 0,
        'totalIncome' => 0,
        'pendingIncome' => 0
    ];

    public function mount()
    {
        $this->loadDashboardStats();
    }

    public function loadDashboardStats()
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }
        
        // Get user roles or empty collection if null
        $userRoles = $user->roles ?? collect([]);
        
        // For admin, show all stats
        if ($userRoles->contains('role_name', 'admin')) {
            $this->stats['totalProperties'] = Property::count();
            $this->stats['totalUnits'] = Unit::count();
            $this->stats['occupiedUnits'] = Unit::where('status', 'occupied')->count();
            $this->stats['vacantUnits'] = Unit::where('status', 'vacant')->count();
            $this->stats['totalRentals'] = Rental::count();
            $this->stats['activeRentals'] = Rental::where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = Invoice::where('payment_status', 'paid')->sum('amount_due');
            $this->stats['pendingIncome'] = Invoice::where('payment_status', 'pending')->sum('amount_due');
        } 
        // For landlord, show only their properties
        elseif ($userRoles->contains('role_name', 'landlord')) {
            $properties = Property::where('landlord_id', $user->user_id)->pluck('property_id')->toArray();
            $units = Unit::whereIn('property_id', $properties)->pluck('room_id')->toArray();
            $rentals = Rental::whereIn('room_id', $units)->pluck('rental_id')->toArray();
            
            $this->stats['totalProperties'] = count($properties);
            $this->stats['totalUnits'] = count($units);
            $this->stats['occupiedUnits'] = Unit::whereIn('room_id', $units)->where('status', 'occupied')->count();
            $this->stats['vacantUnits'] = Unit::whereIn('room_id', $units)->where('status', 'vacant')->count();
            $this->stats['totalRentals'] = count($rentals);
            $this->stats['activeRentals'] = Rental::whereIn('rental_id', $rentals)->where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->sum('amount_due');
            $this->stats['pendingIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->sum('amount_due');
        }
        // For tenant, show only their rentals
        elseif ($userRoles->contains('role_name', 'tenant')) {
            $rentals = Rental::where('tenant_id', $user->user_id)->pluck('rental_id')->toArray();
            
            $this->stats['totalRentals'] = count($rentals);
            $this->stats['activeRentals'] = Rental::where('tenant_id', $user->user_id)->where('status', 'active')->count();
            $this->stats['pendingInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->count();
            $this->stats['paidInvoices'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'paid')->count();
            $this->stats['totalIncome'] = 0; // Tenants don't see income
            $this->stats['pendingIncome'] = Invoice::whereIn('rental_id', $rentals)->where('payment_status', 'pending')->sum('amount_due');
        }
        // Default case - user has no roles
        else {
            // Show minimal or no stats for users without roles
            // This prevents errors when a user doesn't have any roles assigned
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
} 