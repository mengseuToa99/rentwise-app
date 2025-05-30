<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use App\Models\User;
use App\Models\Rental;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantDetail extends Component
{
    public $tenant;
    public $tenantId;
    public $rentals = [];
    public $invoices = [];
    public $paymentHistory = [];
    public $statistics = [];
    
    public function mount($tenant)
    {
        $this->tenantId = $tenant;
        $this->loadTenant();
        $this->loadTenantRentals();
        $this->loadTenantInvoices();
        $this->loadPaymentHistory();
        $this->calculateStatistics();
    }
    
    protected function loadTenant()
    {
        $user = Auth::user();
        
        // First check if this is a valid tenant
        $tenant = User::query()
            ->join('user_roles', 'users.user_id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('roles.role_name', 'tenant')
            ->where('users.user_id', $this->tenantId)
            ->select('users.*')
            ->first();
            
        if (!$tenant) {
            return redirect()->route('tenants.index')->with('error', 'Tenant not found or you do not have permission to view this tenant.');
        }
        
        // For landlords, check if this tenant rents from them
        if ($user->hasRole('landlord')) {
            $hasRental = Rental::where('tenant_id', $this->tenantId)
                ->where('landlord_id', $user->user_id)
                ->exists();
                
            // If not, check if tenant has any rentals
            $hasAnyRental = Rental::where('tenant_id', $this->tenantId)->exists();
            
            // If tenant has rentals but not from this landlord, restrict access
            if ($hasAnyRental && !$hasRental && !$user->hasRole('admin')) {
                return redirect()->route('tenants.index')->with('error', 'You do not have permission to view this tenant.');
            }
        }
        
        $this->tenant = $tenant;
    }
    
    protected function loadTenantRentals()
    {
        $user = Auth::user();
        
        // Get all rentals for this tenant from this landlord
        $query = Rental::query()
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'rental_details.*',
                'room_details.room_number',
                'room_details.room_type',
                'room_details.rent_amount',
                'property_details.property_name',
                'property_details.property_id'
            )
            ->orderBy('rental_details.start_date', 'desc');
            
        // If user is a landlord, only show rentals for their properties
        if ($user->hasRole('landlord')) {
            $query->where('rental_details.landlord_id', $user->user_id);
        }
            
        $this->rentals = $query->get();
    }
    
    protected function loadTenantInvoices()
    {
        $user = Auth::user();
        
        // Get all invoices for this tenant from this landlord
        $query = Invoice::query()
            ->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'invoice_details.*',
                'room_details.room_number',
                'room_details.rent_amount',
                'property_details.property_name',
                'property_details.property_id'
            )
            ->orderBy('invoice_details.due_date', 'desc');
            
        // If user is a landlord, only show invoices for their properties
        if ($user->hasRole('landlord')) {
            $query->where('rental_details.landlord_id', $user->user_id);
        }
            
        $this->invoices = $query->get();
    }
    
    protected function loadPaymentHistory()
    {
        $user = Auth::user();
        
        // Get payment history for this tenant
        $query = DB::table('payment_histories')
            ->join('invoice_details', 'payment_histories.invoice_id', '=', 'invoice_details.invoice_id')
            ->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'payment_histories.*',
                'invoice_details.invoice_id',
                'invoice_details.amount_due as invoice_amount',
                'room_details.room_number',
                'property_details.property_name'
            )
            ->orderBy('payment_histories.payment_date', 'desc');
            
        // If user is a landlord, only show payments for their properties
        if ($user->hasRole('landlord')) {
            $query->where('rental_details.landlord_id', $user->user_id);
        }
            
        $this->paymentHistory = $query->get();
    }
    
    protected function calculateStatistics()
    {
        // Calculate total rent paid
        $totalPaid = $this->paymentHistory->sum('amount');
        
        // Calculate total rent due
        $totalDue = $this->invoices
            ->where('payment_status', 'pending')
            ->sum('amount_due');
        
        // Calculate on-time payment percentage
        $totalInvoices = $this->invoices->count();
        $onTimePayments = $this->invoices
            ->where('payment_status', 'paid')
            ->where('paid_date', '<=', DB::raw('due_date'))
            ->count();
        $onTimePercentage = $totalInvoices > 0 ? round(($onTimePayments / $totalInvoices) * 100) : 0;
        
        // Get current active rental if any
        $activeRental = $this->rentals
            ->where('status', 'active')
            ->first();
        
        // Calculate lease status and remaining days
        $leaseStatus = 'No active lease';
        $remainingDays = 0;
        if ($activeRental) {
            $endDate = \Carbon\Carbon::parse($activeRental->end_date);
            $now = \Carbon\Carbon::now();
            $remainingDays = $now->diffInDays($endDate, false);
            
            if ($remainingDays > 30) {
                $leaseStatus = 'Active';
            } elseif ($remainingDays > 0) {
                $leaseStatus = 'Expiring Soon';
            } else {
                $leaseStatus = 'Expired';
            }
        }
        
        $this->statistics = [
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'on_time_percentage' => $onTimePercentage,
            'lease_status' => $leaseStatus,
            'remaining_days' => $remainingDays,
            'total_rentals' => $this->rentals->count(),
            'active_rentals' => $this->rentals->where('status', 'active')->count(),
            'total_invoices' => $totalInvoices,
            'pending_invoices' => $this->invoices->where('payment_status', 'pending')->count()
        ];
    }
    
    public function render()
    {
        return view('livewire.tenants.tenant-detail', [
            'tenant' => $this->tenant,
            'rentals' => $this->rentals,
            'invoices' => $this->invoices,
            'paymentHistory' => $this->paymentHistory,
            'statistics' => $this->statistics
        ]);
    }
} 