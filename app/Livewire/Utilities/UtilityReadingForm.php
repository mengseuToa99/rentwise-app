<?php

namespace App\Livewire\Utilities;

use Livewire\Component;
use App\Models\Utility;
use App\Models\UtilityUsage;
use App\Models\Unit;
use App\Models\Property;
use App\Models\Rental;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UtilityReadingForm extends Component
{
    public $upcomingDueRentals = [];
    public $utilities = [];
    
    // Form fields for each rental
    public $readings = [];
    
    protected $rules = [
        'readings.*.utility_id' => 'required|exists:utilities,utility_id',
        'readings.*.new_reading' => 'required|numeric|min:0',
    ];
    
    public function mount()
    {
        $user = Auth::user();
        
        // Load utilities
        $this->utilities = Utility::orderBy('utility_name')
            ->get()
            ->mapWithKeys(function($utility) {
                return [$utility->utility_id => $utility->utility_name];
            })->toArray();
        
        // Get rentals that are coming due within 15 days
        $this->loadUpcomingDueRentals();
        
        // Initialize readings array for each rental and utility
        foreach ($this->upcomingDueRentals as $rental) {
            $rentalId = $rental['rental_id'];
            $roomId = $rental['room_id'];
            
            $this->readings[$rentalId] = [];
            
            // Add an entry for each utility
            foreach ($this->utilities as $utilityId => $utilityName) {
                // Get previous reading
                $previousReading = UtilityUsage::where('room_id', $roomId)
                    ->where('utility_id', $utilityId)
                    ->orderBy('usage_date', 'desc')
                    ->first();
                
                // Get utility price
                $utility = Utility::find($utilityId);
                $price = $utility ? $utility->rate_per_unit : 0;
                
                $this->readings[$rentalId][$utilityId] = [
                    'utility_id' => $utilityId,
                    'utility_name' => $utilityName,
                    'old_reading' => $previousReading ? $previousReading->new_meter_reading : 0,
                    'new_reading' => null,
                    'price_per_unit' => $price,
                    'calculated_amount' => 0,
                    'last_reading_date' => $previousReading ? $previousReading->usage_date : null,
                    'usage_date' => Carbon::now()->format('Y-m-d'),
                    'room_id' => $roomId,
                    'create_invoice' => true,
                ];
            }
        }
    }
    
    public function loadUpcomingDueRentals()
    {
        $user = Auth::user();
        
        // Set date range for upcoming rentals (current date to +15 days)
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(15);
        
        // Query to get rentals that are due soon
        $rentalsQuery = Rental::query()
            ->join('users as tenants', 'rental_details.tenant_id', '=', 'tenants.user_id')
            ->join('room_details', 'rental_details.room_id', '=', 'room_details.room_id')
            ->join('property_details', 'room_details.property_id', '=', 'property_details.property_id')
            ->where('rental_details.status', 'active')
            ->whereBetween('rental_details.next_due_date', [$startDate, $endDate])
            ->select(
                'rental_details.*',
                'tenants.first_name',
                'tenants.last_name',
                'room_details.room_number',
                'room_details.room_id',
                'property_details.property_name',
                'property_details.property_id'
            );
            
        if (!$user->roles->contains('role_name', 'admin')) {
            // Landlord can only see their rentals
            $rentalsQuery->where('rental_details.landlord_id', $user->user_id);
        }
        
        $this->upcomingDueRentals = $rentalsQuery->get()->toArray();
    }
    
    public function updatedReadings($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 3 && $parts[2] === 'new_reading') {
            $this->calculateAmount($parts[0], $parts[1]);
        }
    }
    
    public function calculateAmount($rentalId, $utilityId)
    {
        $reading = $this->readings[$rentalId][$utilityId];
        
        if (!is_null($reading['new_reading']) && is_numeric($reading['new_reading'])) {
            $oldReading = $reading['old_reading'];
            $newReading = $reading['new_reading'];
            $pricePerUnit = $reading['price_per_unit'];
            
            $usage = max(0, $newReading - $oldReading);
            $amount = $usage * $pricePerUnit;
            
            $this->readings[$rentalId][$utilityId]['calculated_amount'] = $amount;
            $this->readings[$rentalId][$utilityId]['usage'] = $usage;
        }
    }
    
    public function generateInvoice($rentalId, $utilityId)
    {
        try {
            $reading = $this->readings[$rentalId][$utilityId];
            
            if (!$reading['new_reading']) {
                session()->flash('error', 'Please enter a new reading first');
                return;
            }
            
            DB::beginTransaction();
            
            // Create usage record
            $usage = UtilityUsage::create([
                'room_id' => $reading['room_id'],
                'utility_id' => $reading['utility_id'],
                'usage_date' => $reading['usage_date'],
                'old_meter_reading' => $reading['old_reading'],
                'new_meter_reading' => $reading['new_reading'],
                'amount_used' => $reading['usage'] ?? max(0, $reading['new_reading'] - $reading['old_reading']),
            ]);
            
            // Create invoice if needed
            if ($reading['create_invoice']) {
                // Find the active rental
                $rental = Rental::find($rentalId);
                    
                if (!$rental) {
                    throw new \Exception("Rental not found");
                }
                
                $utility = Utility::find($utilityId);
                $utilityName = $utility ? $utility->utility_name : 'Utility';
                
                // Calculate due date (15 days from now)
                $dueDate = now()->addDays(15);
                
                // Create invoice
                $invoice = Invoice::create([
                    'rental_id' => $rental->rental_id,
                    'amount_due' => $reading['calculated_amount'],
                    'due_date' => $dueDate,
                    'payment_status' => 'pending',
                    'payment_method' => 'cash',
                    'paid' => false,
                    'description' => "{$utilityName} usage ({$reading['usage']} units) from " . 
                        ($reading['last_reading_date'] 
                            ? Carbon::parse($reading['last_reading_date'])->format('d M Y') 
                            : Carbon::parse($reading['usage_date'])->format('d M Y')) . 
                        " to " . Carbon::parse($reading['usage_date'])->format('d M Y')
                ]);
            }
            
            DB::commit();
            
            // Update the readings array
            $this->readings[$rentalId][$utilityId]['generated'] = true;
            
            session()->flash('success', 'Utility reading recorded and invoice generated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.utilities.utility-reading-form');
    }
} 