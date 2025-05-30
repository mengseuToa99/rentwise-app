<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Property;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\UtilityUsage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Rental;
use App\Models\Unit;

class InvoiceForm extends Component
{
    public $invoiceId;
    public $mode = 'create';
    
    // Form fields
    public $selectedProperty = '';
    public $selectedUnit = '';
    public $selectedRental = '';
    public $amount_due;
    public $due_date;
    public $paid = false;
    public $payment_method = 'cash';
    public $payment_status = 'pending';
    
    // Utility readings
    public $readings = [];
    public $utilities = [];
    
    // For dropdown options
    public $properties = [];
    public $units = [];
    public $rentals = [];
    
    protected $rules = [
        'selectedProperty' => 'required|exists:property_details,property_id',
        'selectedUnit' => 'required|exists:room_details,room_id',
        'selectedRental' => 'required|exists:rental_details,rental_id',
        'amount_due' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'paid' => 'boolean',
        'payment_method' => 'required|in:cash,credit_card,bank_transfer',
        'payment_status' => 'required|in:pending,paid,overdue',
    ];
    
    protected $messages = [
        'selectedProperty.required' => 'Please select a property',
        'selectedUnit.required' => 'Please select a unit',
        'selectedRental.required' => 'Please select a rental',
        'amount_due.required' => 'Please enter an amount',
        'amount_due.numeric' => 'Amount must be a number',
        'amount_due.min' => 'Amount must be at least 0',
        'due_date.required' => 'Please select a due date',
    ];
    
    public function mount($invoiceId = null)
    {
        $user = Auth::user();
        
        // Load properties for the landlord
        $this->properties = Property::where('landlord_id', $user->user_id)
            ->orderBy('property_name')
            ->pluck('property_name', 'property_id')
            ->toArray();
            
        // Load utilities
        $this->utilities = Utility::orderBy('utility_name')
            ->pluck('utility_name', 'utility_id')
            ->toArray();
            
        if ($invoiceId) {
            $this->mode = 'edit';
            $this->invoiceId = $invoiceId;
            $this->loadInvoice();
        } else {
            $this->due_date = now()->addDays(15)->format('Y-m-d');
        }
    }
    
    public function loadInvoice()
    {
        $invoice = Invoice::with(['rental.unit.property', 'utilityUsages'])
            ->findOrFail($this->invoiceId);

        // Load basic invoice data
        $this->selectedProperty = $invoice->rental->unit->property_id;
        $this->selectedUnit = $invoice->rental->room_id;
        $this->selectedRental = $invoice->rental_id;
        $this->amount_due = $invoice->amount_due;
        $this->due_date = Carbon::parse($invoice->due_date)->format('Y-m-d');
        $this->paid = $invoice->paid;
        $this->payment_method = $invoice->payment_method;
        $this->payment_status = $invoice->payment_status;

        // Load the units for the selected property
        $this->updatedSelectedProperty($this->selectedProperty);

        // Load the rentals for the selected unit
        $this->updatedSelectedUnit($this->selectedUnit);

        // Load utility readings
        $this->loadReadings();

        // Update readings with actual values
        foreach ($invoice->utilityUsages as $usage) {
            if (isset($this->readings[$usage->utility_id])) {
                $this->readings[$usage->utility_id]['new_reading'] = $usage->new_meter_reading;
                $this->calculateUsage($usage->utility_id);
            }
        }
    }
    
    public function updatedSelectedProperty($value)
    {
        if ($value) {
            $this->units = Unit::where('property_id', $value)
                ->orderBy('room_number')
                ->pluck('room_number', 'room_id')
                ->toArray();
        } else {
            $this->units = [];
        }
        $this->selectedUnit = '';
        $this->selectedRental = '';
        $this->readings = [];
    }
    
    public function updatedSelectedUnit($value)
    {
        if ($value) {
            $this->rentals = Rental::where('room_id', $value)
                ->where('rental_details.status', 'active')
                ->join('users', 'rental_details.tenant_id', '=', 'users.user_id')
                ->select(
                    'rental_details.rental_id',
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) as tenant_name")
                )
                ->pluck('tenant_name', 'rental_id')
                ->toArray();
        } else {
            $this->rentals = [];
        }
        $this->selectedRental = '';
        $this->readings = [];
    }
    
    public function updatedSelectedRental($value)
    {
        if ($value) {
            $this->loadReadings();
        } else {
            $this->readings = [];
        }
    }
    
    public function loadReadings()
    {
        $this->readings = [];
        
        foreach ($this->utilities as $utilityId => $utilityName) {
            // Get the last reading for this unit and utility
            $lastReading = UtilityUsage::where('room_id', $this->selectedUnit)
                ->where('utility_id', $utilityId)
                ->orderBy('usage_date', 'desc')
                ->first();
                
            $previousMeterReading = $lastReading ? $lastReading->new_meter_reading : 0;
            $previousReadingDate = $lastReading ? $lastReading->usage_date : null;
            
            // Initialize reading data
            $this->readings[$utilityId] = [
                'previous_reading' => $previousMeterReading,
                'previous_date' => $previousReadingDate,
                'new_reading' => null,
                'amount_used' => 0,
                'rate' => Utility::find($utilityId)->getCurrentPrice()?->price ?? 0,
                'total_charge' => 0,
                'include' => true
            ];
        }
    }
    
    public function updatedReadings($value, $key)
    {
        if (strpos($key, '.new_reading') !== false) {
            $parts = explode('.', $key);
            $utilityId = $parts[0];
            $this->calculateUsage($utilityId);
        } elseif (strpos($key, '.rate') !== false) {
            $parts = explode('.', $key);
            $utilityId = $parts[0];
            $this->calculateUsage($utilityId);
        }
    }
    
    public function calculateUsage($utilityId)
    {
        if (isset($this->readings[$utilityId]) && 
            is_numeric($this->readings[$utilityId]['new_reading'])) {
            
            $reading = &$this->readings[$utilityId];
            $previous = $reading['previous_reading'] ?? 0;
            $new = $reading['new_reading'];
            
            // Calculate usage
            $amountUsed = max(0, $new - $previous);
            $reading['amount_used'] = $amountUsed;
            
            // Calculate total charge
            $reading['total_charge'] = $amountUsed * $reading['rate'];
            
            // Update total amount due
            $this->amount_due = collect($this->readings)
                ->where('include', true)
                ->sum('total_charge');
        }
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            DB::beginTransaction();
            
            $readingDate = now()->format('Y-m-d');
            
            // Create utility usage records and calculate total amount
            $totalAmount = 0;
            $descriptions = [];
            $utilityUsages = [];
            
            // Validate that at least one utility reading is included
            $hasReadings = false;
            foreach ($this->readings as $utilityId => $reading) {
                if ($reading['include'] && 
                    !empty($reading['new_reading']) && 
                    is_numeric($reading['new_reading']) &&
                    $reading['new_reading'] >= $reading['previous_reading']) {
                    
                    $utility = Utility::find($utilityId);
                    $utilityName = $utility ? $utility->utility_name : 'Utility';
                    
                    // Create utility usage record
                    $usage = UtilityUsage::create([
                        'room_id' => $this->selectedUnit,
                        'utility_id' => $utilityId,
                        'usage_date' => $readingDate,
                        'old_meter_reading' => $reading['previous_reading'],
                        'new_meter_reading' => $reading['new_reading'],
                        'amount_used' => $reading['amount_used'],
                    ]);
                    
                    $utilityUsages[] = $usage->usage_id;
                    $totalAmount += $reading['total_charge'];
                    
                    $previousDate = $reading['previous_date'] 
                        ? Carbon::parse($reading['previous_date'])->format('d M Y') 
                        : 'initial reading';
                        
                    $descriptions[] = "{$utilityName} usage ({$reading['amount_used']} units) from {$previousDate} to " . 
                        Carbon::parse($readingDate)->format('d M Y');
                }
            }
            
            // Create invoice
            $invoice = Invoice::create([
                'rental_id' => $this->selectedRental,
                'amount_due' => $totalAmount,
                'due_date' => $this->due_date,
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'paid' => $this->paid,
                'description' => implode("\n", $descriptions)
            ]);
            
            // Link utility usages to the invoice
            foreach ($utilityUsages as $usageId) {
                DB::table('invoice_utility_usages')->insert([
                    'invoice_id' => $invoice->invoice_id,
                    'usage_id' => $usageId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::commit();
            
            session()->flash('success', 'Invoice created successfully');
            return redirect()->route('invoices.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }
    
    public function cancel()
    {
        // Get all current query parameters
        $params = request()->query();
        
        // Debug the parameters
        logger('Cancel clicked - Current parameters:', $params);
        
        // Get the previous URL parameters from the referrer
        $referrer = request()->header('referer');
        logger('Referrer URL:', ['url' => $referrer]);
        
        if ($referrer) {
            $referrerParams = parse_url($referrer, PHP_URL_QUERY);
            if ($referrerParams) {
                parse_str($referrerParams, $params);
                logger('Parameters from referrer:', $params);
            }
        }
        
        // Redirect to invoices index with preserved parameters
        return redirect()->route('invoices.index', $params);
    }
    
    public function render()
    {
        return view('livewire.invoices.invoice-form');
    }
} 