<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Rental;
use App\Models\Invoice;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $landlord = User::where('username', 'landlord')->first();
        $tenant = User::where('username', 'tenant')->first();

        if (!$landlord || !$tenant) {
            $this->command->error('Landlord or tenant user not found. Run PermissionSeeder first.');
            return;
        }

        // Create properties for the landlord
        $properties = [
            [
                'property_name' => 'Sunrise Apartments',
                'address' => '123 Main Street, Phnom Penh',
                'location' => 'Phnom Penh',
                'total_floors' => 5,
                'total_rooms' => 10,
                'description' => 'Modern apartment complex with all amenities',
                'status' => 'active',
                'landlord_id' => $landlord->user_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'property_name' => 'Riverside Villas',
                'address' => '456 River Road, Siem Reap',
                'location' => 'Siem Reap',
                'total_floors' => 2,
                'total_rooms' => 8,
                'description' => 'Luxurious villas with river view',
                'status' => 'active',
                'landlord_id' => $landlord->user_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'property_name' => 'Palm Residence',
                'address' => '789 Palm Avenue, Sihanoukville',
                'location' => 'Sihanoukville',
                'total_floors' => 4,
                'total_rooms' => 12,
                'description' => 'Comfortable units near the beach',
                'status' => 'active',
                'landlord_id' => $landlord->user_id,
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        foreach ($properties as $propertyData) {
            $property = Property::create($propertyData);
            
            // Create units for each property
            $unitCount = rand(3, 6);
            for ($i = 1; $i <= $unitCount; $i++) {
                $isOccupied = rand(0, 1) == 1;
                $status = $isOccupied ? 'occupied' : 'vacant';
                $roomTypes = ['studio', '1-bedroom', '2-bedroom', '3-bedroom'];
                $roomType = $roomTypes[array_rand($roomTypes)];
                $rent = rand(200, 800);
                
                $unit = Unit::create([
                    'property_id' => $property->property_id,
                    'room_number' => "R" . $i,
                    'room_name' => $property->property_name . " Unit " . $i,
                    'floor_number' => rand(1, 5),
                    'room_type' => $roomType,
                    'description' => ucfirst($roomType) . ' unit with modern amenities',
                    'available' => $status == 'vacant',
                    'status' => $status,
                    'rent_amount' => $rent,
                    'due_date' => $now->copy()->day(rand(1, 28))->format('Y-m-d'),
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
                
                // If the unit is occupied, create a rental agreement
                if ($status == 'occupied') {
                    $startDate = $now->copy()->subMonths(rand(1, 6));
                    $endDate = $startDate->copy()->addYears(1);
                    
                    $rental = Rental::create([
                        'landlord_id' => $landlord->user_id,
                        'tenant_id' => $tenant->user_id,
                        'room_id' => $unit->room_id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'lease_agreement' => null,
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);
                    
                    // Create invoices for the rental
                    $invoiceCount = rand(3, 6);
                    $paymentMethods = ['cash', 'credit_card', 'bank_transfer'];
                    
                    for ($j = 1; $j <= $invoiceCount; $j++) {
                        $invoiceDate = $startDate->copy()->addMonths($j - 1);
                        $dueDate = $invoiceDate->copy()->addDays(15);
                        $isPaid = $j < $invoiceCount - 1; // All except the most recent are paid
                        
                        Invoice::create([
                            'rental_id' => $rental->rental_id,
                            'amount_due' => $rent,
                            'due_date' => $dueDate,
                            'paid' => $isPaid,
                            'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                            'payment_status' => $isPaid ? 'paid' : 'pending',
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }
            }
        }

        $this->command->info('Sample properties, units, rentals, and invoices created successfully.');
    }
} 