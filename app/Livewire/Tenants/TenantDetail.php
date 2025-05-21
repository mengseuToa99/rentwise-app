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
    
    public function mount($tenant)
    {
        $this->tenantId = $tenant;
        $this->loadTenant();
        $this->loadTenantRentals();
        $this->loadTenantInvoices();
        $this->loadPaymentHistory();
    }
    
    protected function loadTenant()
    {
        $user = Auth::user();
        
        // Get the tenant with their rentals from this landlord
        $tenant = User::query()
            ->join('rental_details', 'users.user_id', '=', 'rental_details.tenant_id')
            ->where('rental_details.landlord_id', $user->user_id)
            ->where('users.user_id', $this->tenantId)
            ->select('users.*')
            ->distinct()
            ->first();
            
        if (!$tenant) {
            return redirect()->route('tenants.index')->with('error', 'Tenant not found or you do not have permission to view this tenant.');
        }
        
        $this->tenant = $tenant;
    }
    
    protected function loadTenantRentals()
    {
        $user = Auth::user();
        
        // Get all rentals for this tenant from this landlord
        $this->rentals = Rental::query()
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.landlord_id', $user->user_id)
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'rental_details.*',
                'room_details.room_number',
                'property_details.property_name'
            )
            ->get();
    }
    
    protected function loadTenantInvoices()
    {
        $user = Auth::user();
        
        // Get all invoices for this tenant from this landlord
        $this->invoices = Invoice::query()
            ->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.landlord_id', $user->user_id)
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'invoice_details.*',
                'room_details.room_number',
                'property_details.property_name'
            )
            ->orderBy('invoice_details.due_date', 'desc')
            ->get();
    }
    
    protected function loadPaymentHistory()
    {
        $user = Auth::user();
        
        // Get payment history for this tenant
        $this->paymentHistory = DB::table('payment_histories')
            ->join('invoice_details', 'payment_histories.invoice_id', '=', 'invoice_details.invoice_id')
            ->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.landlord_id', $user->user_id)
            ->where('rental_details.tenant_id', $this->tenantId)
            ->select(
                'payment_histories.payment_id',
                'payment_histories.payment_date',
                'payment_histories.payment_method',
                'payment_histories.payment_amount as amount',
                'invoice_details.invoice_id',
                'invoice_details.amount_due as invoice_amount',
                'room_details.room_number',
                'property_details.property_name'
            )
            ->orderBy('payment_histories.payment_date', 'desc')
            ->get();
    }
    
    public function render()
    {
        return view('livewire.tenants.tenant-detail', [
            'tenant' => $this->tenant,
            'rentals' => $this->rentals,
            'invoices' => $this->invoices,
            'paymentHistory' => $this->paymentHistory
        ]);
    }
} 