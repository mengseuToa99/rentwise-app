<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class InvoiceList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = '';
    public $rentalFilter = '';
    public $propertyFilter = '';
    public $dateFrom;
    public $dateTo;
    public $viewMode = 'all'; // 'all', 'landlord', 'tenant'
    public $displayMode = 'card'; // 'card' or 'table' view mode
    public $perPage = 10; // Default number of invoices per page
    public $dateRange = '';
    public $showCustomDateRange = false;
    
    protected $queryString = ['search', 'statusFilter', 'dateFrom', 'dateTo', 'displayMode', 'perPage', 'propertyFilter', 'dateRange'];
    
    public function mount($viewMode = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Debug incoming parameters
        logger('InvoiceList mount - Request parameters:', request()->query());
        
        // Set the view mode based on the route or parameter
        if (request()->routeIs('tenant.invoices')) {
            $this->viewMode = 'tenant';
        } else {
            $this->viewMode = $viewMode ?? 'all';
        }
        
        // Initialize filters from query string parameters
        $this->search = request()->query('search', '');
        $this->statusFilter = request()->query('statusFilter', '');
        $this->propertyFilter = request()->query('propertyFilter', '');
        $this->dateRange = request()->query('dateRange', '');
        $this->dateFrom = request()->query('dateFrom', '');
        $this->dateTo = request()->query('dateTo', '');
        $this->displayMode = request()->query('displayMode', 'card');
        $this->perPage = request()->query('perPage', 10);
        
        // Debug initialized values
        logger('InvoiceList mount - Initialized values:', [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'propertyFilter' => $this->propertyFilter,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'displayMode' => $this->displayMode,
            'perPage' => $this->perPage
        ]);
        
        // Show custom date range if dates are set but no preset is selected
        if (!empty($this->dateFrom) && !empty($this->dateTo) && empty($this->dateRange)) {
            $this->showCustomDateRange = true;
            $this->dateRange = 'custom';
        }
        
        // If a date range is selected, set the dates accordingly
        if (!empty($this->dateRange) && $this->dateRange !== 'custom') {
            $this->updatedDateRange($this->dateRange);
        }
        
        // Force a re-render to ensure filters are applied
        $this->dispatch('filters-updated');
    }
    
    #[On('redirect')]
    public function handleRedirect($url)
    {
        // Preserve all filter parameters in the URL
        $params = [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'propertyFilter' => $this->propertyFilter,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'displayMode' => $this->displayMode,
            'perPage' => $this->perPage
        ];
        
        // Remove empty parameters
        $params = array_filter($params);
        
        // Redirect with preserved parameters
        return redirect()->to($url . '?' . http_build_query($params));
    }
    
    public function toggleDisplayMode()
    {
        if ($this->displayMode === 'card') {
            $this->displayMode = 'table';
        } else {
            $this->displayMode = 'card';
        }
        $this->resetPage(); // Reset pagination when changing display mode
    }
    
    public function landlordInvoices()
    {
        $this->viewMode = 'landlord';
        return $this->render();
    }
    
    public function tenantInvoices()
    {
        $this->viewMode = 'tenant';
        return $this->render();
    }
    
    public function updatedPerPage()
    {
        $this->resetPage(); // Reset pagination when changing items per page
    }
    
    public function updatedPropertyFilter()
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
    
    public function updatedDateRange($value)
    {
        $this->resetPage();
        
        if ($value === 'custom') {
            $this->showCustomDateRange = true;
            return;
        }
        
        $this->showCustomDateRange = false;
        
        switch ($value) {
            case 'this_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'two_months_ago':
                $this->dateFrom = now()->subMonths(2)->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonths(2)->endOfMonth()->format('Y-m-d');
                break;
            case 'three_months_ago':
                $this->dateFrom = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonths(3)->endOfMonth()->format('Y-m-d');
                break;
            case 'around_two_months':
                $this->dateFrom = now()->subMonths(2)->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->addMonths(2)->endOfMonth()->format('Y-m-d');
                break;
            case 'around_three_months':
                $this->dateFrom = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->addMonths(3)->endOfMonth()->format('Y-m-d');
                break;
            default:
                $this->dateFrom = '';
                $this->dateTo = '';
        }
    }
    
    public function updatedDateFrom()
    {
        $this->resetPage();
        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $this->dateRange = 'custom';
        }
    }
    
    public function updatedDateTo()
    {
        $this->resetPage();
        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $this->dateRange = 'custom';
        }
    }
    
    public function updatedDisplayMode()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $user = Auth::user();
        
        // Get properties for filter dropdown first
        $properties = Property::where('landlord_id', $user->user_id)
            ->orderBy('property_name')
            ->pluck('property_name', 'property_id')
            ->toArray();
        
        // Build the invoice query
        $query = Invoice::query()
            ->join('rental_details', 'invoice_details.rental_id', '=', 'rental_details.rental_id')
            ->join('users as tenants', 'rental_details.tenant_id', '=', 'tenants.user_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select(
                'invoice_details.*',
                DB::raw("CONCAT(tenants.first_name, ' ', tenants.last_name) as tenant_name"),
                'property_details.property_name',
                'property_details.property_id',
                'room_details.room_number'
            );
        
        // Apply view mode filters
        if ($this->viewMode === 'landlord' || $this->viewMode === 'all') {
            $query->where('rental_details.landlord_id', $user->user_id);
        } elseif ($this->viewMode === 'tenant') {
            $query->where('rental_details.tenant_id', $user->user_id);
        }
        
        // Apply property filter
        if (!empty($this->propertyFilter)) {
            $query->where('property_details.property_id', $this->propertyFilter);
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
        
        if ($this->viewMode === 'landlord' || $this->viewMode === 'all') {
            $rentalsQuery->where('rental_details.landlord_id', $user->user_id);
        } elseif ($this->viewMode === 'tenant') {
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
        
        // Get paginated results
        if ($this->perPage === 'all') {
            $invoices = $query->get();
        } else {
            $invoices = $query->paginate($this->perPage);
        }
        
        return view('livewire.invoices.invoice-list', [
            'invoices' => $invoices,
            'rentals' => $rentals,
            'properties' => $properties,
            'viewMode' => $this->viewMode,
            'displayMode' => $this->displayMode,
            'paginationOptions' => $paginationOptions
        ])->layout('components.layouts.app', ['preserveSidebar' => true]);
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
            
            session()->flash('success', 'Invoice marked as paid successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }
} 