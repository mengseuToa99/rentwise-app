<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceForm extends Component
{
    public $invoiceId;
    public $mode = 'create';
    
    // Form fields
    public $rental_id;
    public $amount_due;
    public $due_date;
    public $paid = false;
    public $payment_method = 'cash';
    public $payment_status = 'pending';
    
    // For dropdown options
    public $rentals = [];
    
    protected $rules = [
        'rental_id' => 'required|exists:rental_details,rental_id',
        'amount_due' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'paid' => 'boolean',
        'payment_method' => 'required|in:cash,credit_card,bank_transfer',
        'payment_status' => 'required|in:pending,paid,overdue',
    ];
    
    protected $messages = [
        'rental_id.required' => 'Please select a rental',
        'amount_due.required' => 'Please enter an amount',
        'amount_due.numeric' => 'Amount must be a number',
        'amount_due.min' => 'Amount must be at least 0',
        'due_date.required' => 'Please select a due date',
    ];
    
    public function mount($invoiceId = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        $userRoles = $user->roles ?? collect([]);
        
        // Load rentals based on user role
        $rentalsQuery = Rental::query()
                           ->join('users as tenants', 'rental_details.tenant_id', '=', 'tenants.user_id')
                           ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
                           ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
                           ->select(
                               'rental_details.rental_id',
                               DB::raw("CONCAT(tenants.first_name, ' ', tenants.last_name) as tenant_name"),
                               'property_details.property_name',
                               'room_details.room_number'
                           );
        
        if (!$userRoles->contains('role_name', 'admin')) {
            $rentalsQuery->where('rental_details.landlord_id', $user->user_id);
        }
        
        $rentals = $rentalsQuery->get();
        
        foreach ($rentals as $rental) {
            $this->rentals[$rental->rental_id] = "{$rental->tenant_name} - {$rental->property_name} (Room {$rental->room_number})";
        }
        
        // If editing an existing invoice
        if ($invoiceId) {
            $this->invoiceId = $invoiceId;
            $this->mode = 'edit';
            
            $invoice = Invoice::findOrFail($invoiceId);
            $rental = Rental::find($invoice->rental_id);
            
            // Authorization check
            if (!$userRoles->contains('role_name', 'admin') && $rental && $rental->landlord_id !== $user->user_id) {
                session()->flash('error', 'You are not authorized to edit this invoice');
                return redirect()->route('invoices.index');
            }
            
            // Populate form fields
            $this->rental_id = $invoice->rental_id;
            $this->amount_due = $invoice->amount_due;
            $this->due_date = Carbon::parse($invoice->due_date)->format('Y-m-d');
            $this->paid = $invoice->paid;
            $this->payment_method = $invoice->payment_method;
            $this->payment_status = $invoice->payment_status;
        } else {
            // Set default due date to next month for new invoices
            $this->due_date = Carbon::now()->addMonth()->format('Y-m-d');
        }
    }
    
    public function updatedPaid()
    {
        if ($this->paid) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = Carbon::parse($this->due_date)->isPast() ? 'overdue' : 'pending';
        }
    }
    
    public function updatedDueDate()
    {
        if (!$this->paid) {
            $this->payment_status = Carbon::parse($this->due_date)->isPast() ? 'overdue' : 'pending';
        }
    }
    
    public function updatedPaymentStatus()
    {
        if ($this->payment_status === 'paid') {
            $this->paid = true;
        } else {
            $this->paid = false;
        }
    }
    
    public function save()
    {
        $this->validate();
        
        $user = Auth::user();
        
        try {
            $invoiceData = [
                'rental_id' => $this->rental_id,
                'amount_due' => $this->amount_due,
                'due_date' => $this->due_date,
                'paid' => $this->paid,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
            ];
            
            if ($this->mode === 'edit') {
                $invoice = Invoice::findOrFail($this->invoiceId);
                $invoice->update($invoiceData);
                session()->flash('success', 'Invoice updated successfully');
            } else {
                Invoice::create($invoiceData);
                session()->flash('success', 'Invoice created successfully');
            }
            
            return redirect()->route('invoices.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save invoice: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.invoices.invoice-form');
    }
} 