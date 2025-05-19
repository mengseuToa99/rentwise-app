<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceDisplay extends Component
{
    use WithPagination;
    
    public $invoice;
    public $invoiceId;
    public $isFlipped = false;
    
    // Search and filtering
    public $search = '';
    public $statusFilter = '';
    public $rentalFilter = '';
    public $dateFrom;
    public $dateTo;
    public $perPage = 10;
    
    protected $queryString = ['search', 'statusFilter', 'rentalFilter', 'dateFrom', 'dateTo', 'perPage'];
    
    public function mount($invoiceId = null)
    {
        if ($invoiceId) {
        $this->invoiceId = $invoiceId;
        $this->loadInvoice();
        }
    }
    
    public function loadInvoice()
    {
        if ($this->invoiceId) {
        $this->invoice = Invoice::with(['rental', 'rental.tenant', 'rental.unit', 'rental.unit.property'])
            ->findOrFail($this->invoiceId);
        }
    }
    
    public function toggleFlip()
    {
        $this->isFlipped = !$this->isFlipped;
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatedRentalFilter()
    {
        $this->resetPage();
    }
    
    public function updatedDateFrom()
    {
        $this->resetPage();
    }
    
    public function updatedDateTo()
    {
        $this->resetPage();
    }
    
    public function deleteInvoice($invoiceId)
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                session()->flash('error', 'Invoice not found');
                return;
            }
            
            // Verify authorization
            $user = Auth::user();
            $rental = Rental::find($invoice->rental_id);
            
            if (!$rental) {
                session()->flash('error', 'Related rental not found');
                return;
            }
            
            if ($rental->landlord_id !== $user->user_id) {
                session()->flash('error', 'You are not authorized to delete this invoice');
                return;
            }
            
            $invoice->delete();
            
            session()->flash('success', 'Invoice deleted successfully');
            return redirect()->route('tenant.invoices');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
    
    public function markAsPaid($invoiceId)
    {
        try {
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                session()->flash('error', 'Invoice not found');
                return;
            }
            
            // Verify authorization
            $user = Auth::user();
            $rental = Rental::find($invoice->rental_id);
            
            if (!$rental) {
                session()->flash('error', 'Related rental not found');
                return;
            }
            
            if ($rental->landlord_id !== $user->user_id) {
                session()->flash('error', 'You are not authorized to update this invoice');
                return;
            }
            
            $invoice->paid = true;
            $invoice->payment_status = 'paid';
            $invoice->save();
            
            $this->loadInvoice(); // Reload the invoice to reflect changes
            session()->flash('success', 'Invoice marked as paid successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        // Get the current user
        $user = Auth::user();
        
        // Initialize query for fetching invoices
        $query = Invoice::query();
        
        // Join necessary tables for filtering
        $query->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
              ->join('users as tenants', 'rental_details.tenant_id', '=', 'tenants.user_id')
              ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
              ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
              ->select('invoice_details.*', 
                      DB::raw("CONCAT(tenants.first_name, ' ', tenants.last_name) as tenant_name"), 
                      'property_details.property_name', 
                      'room_details.room_number');
        
        // Apply user role-based filter
        if ($user->role === 'landlord') {
            $query->where('rental_details.landlord_id', $user->user_id);
        } elseif ($user->role === 'tenant') {
            $query->where('rental_details.tenant_id', $user->user_id);
        }
        
        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('invoice_details.payment_status', $this->statusFilter);
        }
        
        // Apply rental filter
        if (!empty($this->rentalFilter)) {
            $query->where('invoice_details.rental_id', $this->rentalFilter);
        }
        
        // Apply date range filter
        if (!empty($this->dateFrom)) {
            $query->where('invoice_details.due_date', '>=', $this->dateFrom);
        }
        if (!empty($this->dateTo)) {
            $query->where('invoice_details.due_date', '<=', $this->dateTo);
        }
        
        // Apply search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('tenants.first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('tenants.last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('property_details.property_name', 'like', '%' . $this->search . '%')
                  ->orWhere('room_details.room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('invoice_details.invoice_id', 'like', '%' . $this->search . '%');
            });
        }
        
        // Order by most recent first
        $query->orderBy('invoice_details.created_at', 'desc');
        
        // Get rentals for filter dropdown
        $rentalsQuery = Rental::query()
                          ->join('users as tenants', 'rental_details.tenant_id', '=', 'tenants.user_id')
                          ->select('rental_details.rental_id', DB::raw("CONCAT(tenants.first_name, ' ', tenants.last_name) as tenant_name"));
        
        if ($user->role === 'landlord') {
            $rentalsQuery->where('rental_details.landlord_id', $user->user_id);
        } elseif ($user->role === 'tenant') {
            $rentalsQuery->where('rental_details.tenant_id', $user->user_id);
        }
        
        $rentals = $rentalsQuery->pluck('tenant_name', 'rental_id');
        
        // Define pagination options
        $paginationOptions = [
            10 => '10',
            25 => '25',
            50 => '50',
            100 => '100',
            'all' => 'All'
        ];
        
        // Get invoices with pagination if needed
        if (!$this->invoiceId) {
            if ($this->perPage === 'all') {
                $invoices = $query->get();
            } else {
                $invoices = $query->paginate($this->perPage);
            }
        } else {
            $invoices = null; // Not needed when viewing a specific invoice
        }
        
        return view('livewire.invoices.invoice-display', [
            'rentals' => $rentals,
            'paginationOptions' => $paginationOptions,
            'invoices' => $invoices
        ])->layout('components.layouts.app', ['preserveSidebar' => true]);
    }
}
