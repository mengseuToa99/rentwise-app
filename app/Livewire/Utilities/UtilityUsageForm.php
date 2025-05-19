<?php

namespace App\Livewire\Utilities;

use Livewire\Component;
use App\Models\Utility;
use App\Models\UtilityUsage;
use App\Models\Unit;
use App\Models\Rental;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class UtilityUsageForm extends Component
{
    public $room_id;
    public $utility_id;
    public $old_meter_reading = 0;
    public $new_meter_reading;
    public $usage_date;
    public $amount_used = 0;
    public $calculated_charge = 0;
    public $create_invoice = true;
    public $due_date;
    
    // For dropdowns
    public $units = [];
    public $utilities = [];
    
    // For display information
    public $selectedUnit = null;
    public $selectedUtility = null;
    public $previousReading = null;
    public $currentPrice = null;
    
    protected $listeners = ['propertySelected' => 'loadUnits'];
    
    protected $rules = [
        'room_id' => 'required|exists:room_details,room_id',
        'utility_id' => 'required|exists:utilities,utility_id',
        'new_meter_reading' => 'required|numeric|min:0',
        'usage_date' => 'required|date|before_or_equal:today',
        'create_invoice' => 'boolean',
        'due_date' => 'required_if:create_invoice,true|nullable|date|after_or_equal:today',
    ];
    
    public function mount()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Set default dates
        $this->usage_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(14)->format('Y-m-d');
        
        // Load utilities
        $this->utilities = Utility::orderBy('utility_name')->get()
            ->mapWithKeys(function($utility) {
                return [$utility->utility_id => $utility->utility_name];
            })->toArray();
            
        // Load units based on user's role and properties
        $user = Auth::user();
        
        $unitsQuery = Unit::query()
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select('room_details.*', 'property_details.property_name');
            
        if (!$user->hasRole('admin')) {
            // If not admin, only show units from properties they own
            $unitsQuery->where('property_details.landlord_id', $user->user_id);
        }
        
        $this->units = $unitsQuery->get()
            ->mapWithKeys(function($unit) {
                return [$unit->room_id => "{$unit->property_name} - Room {$unit->room_number}"];
            })->toArray();
    }
    
    public function updatedRoomId()
    {
        $this->loadPreviousReading();
        $this->selectedUnit = Unit::join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select('room_details.*', 'property_details.property_name')
            ->where('room_details.room_id', $this->room_id)
            ->first();
    }
    
    public function updatedUtilityId()
    {
        $this->loadPreviousReading();
        $this->selectedUtility = Utility::find($this->utility_id);
        $this->currentPrice = $this->selectedUtility ? $this->selectedUtility->getCurrentPrice() : null;
    }
    
    public function loadPreviousReading()
    {
        if ($this->room_id && $this->utility_id) {
            $this->previousReading = UtilityUsage::where('room_id', $this->room_id)
                ->where('utility_id', $this->utility_id)
                ->orderBy('usage_date', 'desc')
                ->first();
                
            if ($this->previousReading) {
                $this->old_meter_reading = $this->previousReading->new_meter_reading;
            } else {
                $this->old_meter_reading = 0;
            }
            
            $this->calculateUsage();
        }
    }
    
    public function updatedNewMeterReading()
    {
        $this->calculateUsage();
    }
    
    public function calculateUsage()
    {
        if (is_numeric($this->new_meter_reading) && is_numeric($this->old_meter_reading)) {
            $this->amount_used = max(0, $this->new_meter_reading - $this->old_meter_reading);
            $this->calculateCharge();
        }
    }
    
    public function calculateCharge()
    {
        if ($this->utility_id) {
            $utility = Utility::find($this->utility_id);
            $price = $utility ? $utility->getCurrentPrice() : null;
            
            if ($price) {
                $this->calculated_charge = $this->amount_used * $price->price;
            }
        }
    }
    
    public function calculate()
    {
        if (!$this->utility_id || !$this->room_id) {
            return;
        }
        
        // Get the utility and room
        $utility = Utility::find($this->utility_id);
        $room = Room::find($this->room_id);
        
        if (!$utility || !$room) {
            return;
        }
        
        // Get latest reading for this utility and room
        $this->previousReading = UtilityUsage::where('utility_id', $this->utility_id)
            ->where('room_id', $this->room_id)
            ->latest('usage_date')
            ->first();
        
        if ($this->previousReading) {
            $this->old_meter_reading = $this->previousReading->new_meter_reading;
        } else {
            // If no previous reading, start from 0 or a default value
            $this->old_meter_reading = 0;
        }
        
        $this->calculateUsageAndCharge();
    }
    
    public function calculateUsageAndCharge()
    {
        if ($this->new_meter_reading === null || $this->old_meter_reading === null) {
            return;
        }
        
        // Calculate usage
        $this->amount_used = max(0, $this->new_meter_reading - $this->old_meter_reading);
        
        // Get the unit price
        $utility = Utility::find($this->utility_id);
        
        if ($utility) {
            $this->rate_per_unit = $utility->rate_per_unit;
            // Calculate charge
            $this->calculated_charge = $this->amount_used * $this->rate_per_unit;
        }
    }
    
    public function updated($name, $value)
    {
        if ($name === 'new_meter_reading' || $name === 'rate_per_unit') {
            $this->calculateUsageAndCharge();
        }
        
        if ($name === 'utility_id' || $name === 'room_id') {
            $this->calculate();
        }
    }
    
    public function save()
    {
        $this->validate();
        
        try {
            // Start a database transaction
            DB::beginTransaction();
            
            // Create usage record
            $usage = UtilityUsage::create([
                'room_id' => $this->room_id,
                'utility_id' => $this->utility_id,
                'usage_date' => $this->usage_date,
                'old_meter_reading' => $this->old_meter_reading,
                'new_meter_reading' => $this->new_meter_reading,
                'amount_used' => $this->amount_used,
            ]);
            
            // Create invoice if needed
            if ($this->create_invoice) {
                // Find the active rental for this room
                $rental = Rental::where('room_id', $this->room_id)
                    ->where('status', 'active')
                    ->first();
                    
                if (!$rental) {
                    throw new \Exception("No active rental found for this room");
                }
                
                // Get utility name for description
                $utility = Utility::find($this->utility_id);
                $utilityName = $utility ? $utility->utility_name : 'Utility';
                
                // Create invoice
                $invoice = Invoice::create([
                    'rental_id' => $rental->rental_id,
                    'amount_due' => $this->calculated_charge,
                    'due_date' => $this->due_date,
                    'payment_status' => 'pending',
                    'payment_method' => 'cash',
                    'paid' => false,
                    'description' => "{$utilityName} usage ({$this->amount_used} units) from " . 
                        Carbon::parse($this->previousReading?->usage_date ?? $this->usage_date)->format('d M Y') . 
                        " to " . Carbon::parse($this->usage_date)->format('d M Y')
                ]);
                
                // Send notification to tenant
                try {
                    $tenant = User::find($rental->tenant_id);
                    if ($tenant && $tenant->email) {
                        // You can implement email notification here
                        // Mail::to($tenant->email)->send(new InvoiceCreated($invoice));
                    }
                } catch (\Exception $e) {
                    // Log the error but continue with the process
                    \Log::error("Failed to send invoice notification: " . $e->getMessage());
                }
            }
            
            DB::commit();
            
            session()->flash('success', 'Utility usage recorded successfully' . 
                ($this->create_invoice ? ' and invoice created' : ''));
                
            return redirect()->route('utilities.usage.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to save utility usage: ' . $e->getMessage());
        }
    }
    
    public function resetForm()
    {
        $this->room_id = null;
        $this->utility_id = null;
        $this->new_meter_reading = null;
        $this->amount_used = 0;
        $this->calculated_charge = 0;
        $this->create_invoice = true;
        $this->due_date = now()->addDays(14)->format('Y-m-d');
        $this->usage_date = now()->format('Y-m-d');
        
        $this->selectedUnit = null;
        $this->selectedUtility = null;
        $this->previousReading = null;
        $this->currentPrice = null;
        
        $this->resetValidation();
    }
    
    public function render()
    {
        return view('livewire.utilities.utility-usage-form');
    }
} 