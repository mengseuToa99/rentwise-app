<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Rental;
use Carbon\Carbon;
use Faker\Factory as Faker;

class RentalAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get landlords (excluding the default 'landlord' user)
        $landlords = User::whereHas('roles', function($query) {
            $query->where('role_name', 'landlord');
        })->where('username', 'like', 'landlord%')->get();
        
        // Get tenants (excluding the default 'tenant' user)
        $tenants = User::whereHas('roles', function($query) {
            $query->where('role_name', 'tenant');
        })->where('username', 'like', 'tenant%')->get();
        
        if ($landlords->count() < 5) {
            $this->command->error('Not enough landlords found. Need at least 5 landlords.');
            return;
        }
        
        if ($tenants->count() < 15) {
            $this->command->error('Not enough tenants found. Need at least 15 tenants (3-5 per landlord).');
            return;
        }
        
        // Delete existing rental records
        $this->command->info('Deleting existing rental records...');
        \DB::table('rental_details')->delete();
        
        // Shuffle the tenants to randomize the assignment
        $tenants = $tenants->shuffle();
        
        // Take only the first 5 landlords to ensure consistency
        $landlords = $landlords->take(5);
        
        $tenantIndex = 0;
        $totalRentals = 0;
        
        // For each landlord, assign 3-5 tenants
        foreach ($landlords as $index => $landlord) {
            // Get properties for this landlord
            $properties = Property::where('landlord_id', $landlord->user_id)->get();
            
            if ($properties->count() === 0) {
                $this->command->error("Landlord {$landlord->username} has no properties. Skipping.");
                continue;
            }
            
            // Determine how many tenants to assign to this landlord (3-5)
            $tenantsForThisLandlord = rand(3, 5);
            
            // Make sure we don't exceed the number of available tenants
            $tenantsForThisLandlord = min($tenantsForThisLandlord, $tenants->count() - $tenantIndex);
            
            $this->command->info("Assigning {$tenantsForThisLandlord} tenants to landlord {$landlord->username} ({$landlord->first_name} {$landlord->last_name})");
            
            // Keep track of assigned units to avoid duplicates
            $assignedUnits = [];
            
            // Assign tenants to this landlord
            for ($i = 0; $i < $tenantsForThisLandlord; $i++) {
                if ($tenantIndex >= $tenants->count()) {
                    break; // No more tenants available
                }
                
                $tenant = $tenants[$tenantIndex];
                $tenantIndex++;
                
                // Get a random property for this landlord
                $property = $properties->random();
                
                // Get available units for this property
                $units = Unit::where('property_id', $property->property_id)->get();
                
                if ($units->count() === 0) {
                    $this->command->error("Property {$property->property_name} has no units. Skipping tenant.");
                    continue;
                }
                
                // Try to find an unassigned unit
                $unit = null;
                foreach ($units as $potentialUnit) {
                    $key = $property->property_id . '-' . $potentialUnit->room_id;
                    if (!in_array($key, $assignedUnits)) {
                        $unit = $potentialUnit;
                        $assignedUnits[] = $key;
                        break;
                    }
                }
                
                // If no unassigned unit found, create a new one
                if (!$unit) {
                    $unit = $units->random();
                }
                
                // Create a random start date within the last 12 months
                $startDate = Carbon::now()->subMonths(rand(1, 12))->startOfDay();
                
                // Create a random lease length (6, 12, or 24 months)
                $leaseLength = $faker->randomElement([6, 12, 24]);
                
                // Calculate end date
                $endDate = (clone $startDate)->addMonths($leaseLength);
                
                // Set status based on dates
                $status = 'active';
                if ($endDate->isPast()) {
                    $status = 'expired';
                } elseif ($startDate->isFuture()) {
                    $status = 'pending';
                }
                
                // Create rental record
                \DB::table('rental_details')->insert([
                    'tenant_id' => $tenant->user_id,
                    'landlord_id' => $landlord->user_id,
                    'room_id' => $unit->room_id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'lease_agreement' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                // Update unit status
                $unit->update([
                    'available' => false,
                    'status' => 'occupied'
                ]);
                
                $this->command->info("  → Created rental for tenant {$tenant->first_name} {$tenant->last_name} in {$property->property_name}, unit {$unit->room_number}");
                $totalRentals++;
            }
        }
        
        $this->command->info('Created ' . $totalRentals . ' rental records across ' . $landlords->count() . ' landlords.');
    }
} 