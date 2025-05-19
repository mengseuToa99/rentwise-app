<?php

namespace App\Livewire\Invoices;

use Livewire\Component;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Invoice;
use App\Models\Utility;
use App\Models\UtilityUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BulkInvoiceGenerator extends Component
{
    public $readings = [];
    public $utilities = [];
    public $processedUnits = [];
    
    public function mount()
    {
        // Load available utilities
        $this->utilities = Utility::orderBy('utility_name')
            ->pluck('utility_name', 'utility_id')
            ->toArray();
            
        // Load suggested readings
        $this->loadSuggestedReadings();
    }
    
    public function loadSuggestedReadings()
    {
        $user = Auth::user();
        $today = now();
        
        // Get all active rentals for the landlord's properties
        $rentals = Rental::join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('users', 'rental_details.tenant_id', '=', 'users.user_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->select(
                'rental_details.*', 
                'room_details.room_number',
                'room_details.room_id',
                'property_details.property_name',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as tenant_name")
            )
            ->where('property_details.landlord_id', $user->user_id)
            ->where('rental_details.status', 'active')
            ->get();
            
        // Get all utilities
        $utilities = Utility::all();
        
        // Initialize readings array
        $this->readings = [];
        
        foreach ($rentals as $rental) {
            // Calculate the next due date based on rental start date
            $startDate = Carbon::parse($rental->start_date);
            $nextDueDate = $this->calculateNextDueDate($startDate);
            
            // Skip if the next due date is more than 5 days in the future
            if ($nextDueDate->diffInDays($today) > 5) {
                continue;
            }
            
            // Check if an invoice already exists for this month
            $existingInvoice = Invoice::where('rental_id', $rental->rental_id)
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->first();
                
            if ($existingInvoice) {
                continue;
            }
            
            foreach ($utilities as $utility) {
                // Get the last reading for this unit and utility
                $lastReading = UtilityUsage::where('room_id', $rental->room_id)
                    ->where('utility_id', $utility->utility_id)
                    ->orderBy('usage_date', 'desc')
                    ->first();
                    
                // Always generate a reading for active rentals
                $previousMeterReading = $lastReading ? $lastReading->new_meter_reading : 0;
                $previousReadingDate = $lastReading ? $lastReading->usage_date : null;
                
                // Get current utility rate
                $currentRate = $utility->getCurrentPrice() ? $utility->getCurrentPrice()->price : 0;
                
                // Initialize reading data for this unit and utility
                $this->readings[$rental->room_id][$utility->utility_id] = [
                    'room_id' => $rental->room_id,
                    'rental_id' => $rental->rental_id,
                    'room_number' => $rental->room_number,
                    'tenant_name' => $rental->tenant_name,
                    'property_name' => $rental->property_name,
                    'previous_reading' => $previousMeterReading,
                    'previous_date' => $previousReadingDate,
                    'new_reading' => null,
                    'amount_used' => 0,
                    'rate' => $currentRate,
                    'total_charge' => 0,
                    'include' => true,
                    'due_date' => $nextDueDate->format('Y-m-d')
                ];
            }
        }
    }
    
    protected function calculateNextDueDate($startDate)
    {
        $today = now();
        $startDay = $startDate->day;
        
        // If we're past the start day this month, use next month
        if ($today->day > $startDay) {
            return $today->copy()->addMonth()->setDay($startDay);
        }
        
        // Otherwise use this month
        return $today->copy()->setDay($startDay);
    }
    
    public function updatedReadings($value, $key)
    {
        // Check if the key is for a new reading value
        if (strpos($key, '.new_reading') !== false) {
            $parts = explode('.', $key);
            $roomId = $parts[0];
            $utilityId = $parts[1];
            $this->calculateUsage($roomId, $utilityId);
        }
    }
    
    public function calculateUsage($roomId, $utilityId)
    {
        if (isset($this->readings[$roomId][$utilityId]) && 
            is_numeric($this->readings[$roomId][$utilityId]['new_reading'])) {
            
            $reading = &$this->readings[$roomId][$utilityId];
            $previous = $reading['previous_reading'] ?? 0;
            $new = $reading['new_reading'];
            
            // Calculate usage
            $amountUsed = max(0, $new - $previous);
            $reading['amount_used'] = $amountUsed;
            
            // Calculate total charge using the rate from utility settings
            $reading['total_charge'] = $amountUsed * $reading['rate'];
        }
    }
    
    public function generateSingleInvoice($roomId)
    {
        try {
            DB::beginTransaction();
            
            $invoicesCreated = 0;
            $readingDate = now()->format('Y-m-d');
            
            if (isset($this->readings[$roomId])) {
                $dueDate = $this->readings[$roomId][array_key_first($this->readings[$roomId])]['due_date'];
                
                foreach ($this->readings[$roomId] as $utilityId => $reading) {
                    if ($reading['include'] && 
                        !empty($reading['new_reading']) && 
                        is_numeric($reading['new_reading']) &&
                        $reading['new_reading'] >= $reading['previous_reading']) {
                        
                        $utility = Utility::find($utilityId);
                        $utilityName = $utility ? $utility->utility_name : 'Utility';
                        
                        // Create utility usage record
                        UtilityUsage::create([
                            'room_id' => $roomId,
                            'utility_id' => $utilityId,
                            'usage_date' => $readingDate,
                            'old_meter_reading' => $reading['previous_reading'],
                            'new_meter_reading' => $reading['new_reading'],
                            'amount_used' => $reading['amount_used'],
                        ]);
                        
                        // Create invoice
                        $previousDate = $reading['previous_date'] 
                            ? Carbon::parse($reading['previous_date'])->format('d M Y') 
                            : 'initial reading';
                            
                        Invoice::create([
                            'rental_id' => $reading['rental_id'],
                            'amount_due' => $reading['total_charge'],
                            'due_date' => $dueDate,
                            'payment_status' => 'pending',
                            'payment_method' => 'cash',
                            'paid' => false,
                            'description' => "{$utilityName} usage ({$reading['amount_used']} units) from {$previousDate} to " . 
                                Carbon::parse($readingDate)->format('d M Y')
                        ]);
                        
                        $invoicesCreated++;
                    }
                }
                
                // Mark this unit as processed
                $this->processedUnits[] = $roomId;
                
                // Remove the unit from readings
                unset($this->readings[$roomId]);
            }
            
            DB::commit();
            
            session()->flash('success', "Successfully created {$invoicesCreated} invoices for this unit");
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }
    
    public function generateInvoices()
    {
        try {
            DB::beginTransaction();
            
            $invoicesCreated = 0;
            $readingDate = now()->format('Y-m-d');
            
            foreach ($this->readings as $roomId => $utilityReadings) {
                $dueDate = $utilityReadings[array_key_first($utilityReadings)]['due_date'];
                
                foreach ($utilityReadings as $utilityId => $reading) {
                    if ($reading['include'] && 
                        !empty($reading['new_reading']) && 
                        is_numeric($reading['new_reading']) &&
                        $reading['new_reading'] >= $reading['previous_reading']) {
                        
                        $utility = Utility::find($utilityId);
                        $utilityName = $utility ? $utility->utility_name : 'Utility';
                        
                        // Create utility usage record
                        UtilityUsage::create([
                            'room_id' => $roomId,
                            'utility_id' => $utilityId,
                            'usage_date' => $readingDate,
                            'old_meter_reading' => $reading['previous_reading'],
                            'new_meter_reading' => $reading['new_reading'],
                            'amount_used' => $reading['amount_used'],
                        ]);
                        
                        // Create invoice
                        $previousDate = $reading['previous_date'] 
                            ? Carbon::parse($reading['previous_date'])->format('d M Y') 
                            : 'initial reading';
                            
                        Invoice::create([
                            'rental_id' => $reading['rental_id'],
                            'amount_due' => $reading['total_charge'],
                            'due_date' => $dueDate,
                            'payment_status' => 'pending',
                            'payment_method' => 'cash',
                            'paid' => false,
                            'description' => "{$utilityName} usage ({$reading['amount_used']} units) from {$previousDate} to " . 
                                Carbon::parse($readingDate)->format('d M Y')
                        ]);
                        
                        $invoicesCreated++;
                    }
                }
                
                // Mark this unit as processed
                $this->processedUnits[] = $roomId;
            }
            
            // Clear all readings after successful generation
            $this->readings = [];
            
            DB::commit();
            
            session()->flash('success', "Successfully created {$invoicesCreated} invoices");
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to generate invoices: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.invoices.bulk-invoice-generator');
    }
}
