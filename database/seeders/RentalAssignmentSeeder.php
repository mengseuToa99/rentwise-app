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
        
        // Get landlords
        $landlords = User::whereHas('roles', function($query) {
            $query->where('role_name', 'landlord');
        })->get();
        
        // Get tenants
        $tenants = User::whereHas('roles', function($query) {
            $query->where('role_name', 'tenant');
        })->where('username', 'like', 'tenant%')->get();
        
        if ($tenants->count() === 0) {
            $this->command->error('No tenants found. Please run the LandlordTenantSeeder first.');
            return;
        }
        
        // Delete existing rental records
        $this->command->info('Deleting existing rental records...');
        \DB::table('rental_details')->delete();
        
        $rentals = [];
        $tenantIndex = 0;
        
        // For each landlord
        foreach ($landlords as $landlord) {
            // Get their properties
            $properties = Property::where('landlord_id', $landlord->user_id)->get();
            
            foreach ($properties as $property) {
                // Get available units for this property
                $units = Unit::where('property_id', $property->property_id)->get();
                
                foreach ($units as $unit) {
                    // Skip if we've run out of tenants
                    if ($tenantIndex >= $tenants->count()) {
                        break;
                    }
                    
                    $tenant = $tenants[$tenantIndex];
                    $tenantIndex++;
                    
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
                    
                    // Create rental record using only the columns that exist
                    $rental = \DB::table('rental_details')->insert([
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
                    
                    $this->command->info("Created rental for tenant {$tenant->first_name} {$tenant->last_name} in {$property->property_name}, unit {$unit->room_number}");
                    $rentals[] = $rental;
                    
                    // Update unit status
                    $unit->update([
                        'available' => false,
                        'status' => 'occupied'
                    ]);
                }
            }
        }
        
        $this->command->info('Created ' . count($rentals) . ' rental records.');
    }
} 