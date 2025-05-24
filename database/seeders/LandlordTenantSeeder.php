<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Property;
use App\Models\Unit;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LandlordTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get role IDs
        $landlordRole = Role::where('role_name', 'landlord')->first();
        $tenantRole = Role::where('role_name', 'tenant')->first();
        
        if (!$landlordRole || !$tenantRole) {
            $this->command->error('Roles not found! Please run RoleSeeder first.');
            return;
        }
        
        // Create 3 landlords
        $landlords = [];
        for ($i = 1; $i <= 3; $i++) {
            // Check if user already exists
            $user = User::where('username', 'landlord' . $i)->first();
            
            if (!$user) {
                $user = User::create([
                    'username' => 'landlord' . $i,
                    'email' => 'landlord' . $i . '@example.com',
                    'password_hash' => Hash::make('password'),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone_number' => $faker->phoneNumber,
                    'status' => 'active',
                ]);
                
                // Attach landlord role
                $user->roles()->attach($landlordRole->role_id);
                
                $this->command->info("Created landlord: {$user->username}");
            } else {
                $this->command->info("Landlord {$user->username} already exists");
            }
            
            $landlords[] = $user;
        }
        
        // Create 10 tenants
        $tenants = [];
        for ($i = 1; $i <= 10; $i++) {
            // Check if user already exists
            $user = User::where('username', 'tenant' . $i)->first();
            
            if (!$user) {
                $user = User::create([
                    'username' => 'tenant' . $i,
                    'email' => 'tenant' . $i . '@example.com',
                    'password_hash' => Hash::make('password'),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone_number' => $faker->phoneNumber,
                    'status' => 'active',
                ]);
                
                // Attach tenant role
                $user->roles()->attach($tenantRole->role_id);
                
                $this->command->info("Created tenant: {$user->username}");
            } else {
                $this->command->info("Tenant {$user->username} already exists");
            }
            
            $tenants[] = $user;
        }
        
        // Create properties for each landlord (random number up to 5)
        foreach ($landlords as $landlord) {
            // Check how many properties this landlord already has
            $existingPropertiesCount = Property::where('landlord_id', $landlord->user_id)->count();
            
            // If they already have 5 or more properties, skip
            if ($existingPropertiesCount >= 5) {
                $this->command->info("Landlord {$landlord->username} already has {$existingPropertiesCount} properties, skipping");
                continue;
            }
            
            // Calculate how many more properties to create (up to 5 total)
            $propertyCount = rand(1, 5 - $existingPropertiesCount);
            
            for ($i = 1; $i <= $propertyCount; $i++) {
                $property = Property::create([
                    'landlord_id' => $landlord->user_id,
                    'property_name' => $faker->company . ' Property',
                    'house_building_number' => $faker->buildingNumber,
                    'street' => $faker->streetName,
                    'village' => $faker->city,
                    'commune' => $faker->city,
                    'district' => $faker->city,
                    'total_floors' => rand(1, 5),
                    'total_rooms' => rand(1, 10),
                    'description' => $faker->paragraph,
                    'status' => 'active',
                    'property_type' => $faker->randomElement(['apartment', 'house', 'condo', 'villa']),
                    'year_built' => $faker->year,
                    'property_size' => rand(50, 500),
                    'size_measurement' => 'sqm',
                    'is_pets_allowed' => $faker->boolean,
                ]);
                
                $this->command->info("Created property: {$property->property_name} for landlord {$landlord->username}");
                
                // Create units for each property (random number up to 6)
                $unitCount = rand(1, 6);
                
                for ($j = 1; $j <= $unitCount; $j++) {
                    // Set due date to a random day of the current month
                    $dueDay = rand(1, 28);
                    $dueDate = Carbon::now()->setDay($dueDay)->startOfDay();
                    
                    $unit = Unit::create([
                        'property_id' => $property->property_id,
                        'room_number' => 'R' . $j,
                        'floor_number' => rand(1, $property->total_floors),
                        'room_name' => $faker->randomElement(['Studio', '1BR', '2BR', 'Deluxe', 'Standard', 'Premium']) . ' ' . $j,
                        'room_type' => $faker->randomElement(['studio', 'one_bedroom', 'two_bedroom', 'three_bedroom']),
                        'type' => $faker->randomElement(['studio', 'one_bedroom', 'two_bedroom', 'three_bedroom']),
                        'description' => $faker->paragraph,
                        'available' => $faker->boolean(70), // 70% chance of being available
                        'status' => $faker->randomElement(['vacant', 'occupied']), // Only use valid enum values
                        'rent_amount' => $faker->numberBetween(300, 2000),
                        'due_date' => $dueDate,
                    ]);
                    
                    $this->command->info("Created unit: {$unit->room_name} in property {$property->property_name}");
                }
            }
        }
        
        $this->command->info('Seeding completed successfully!');
    }
} 