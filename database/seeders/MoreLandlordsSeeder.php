<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Property;
use App\Models\Unit;
use Faker\Factory as Faker;

class MoreLandlordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get landlord role
        $landlordRole = Role::where('role_name', 'landlord')->first();
        
        if (!$landlordRole) {
            $this->command->error('Landlord role not found!');
            return;
        }
        
        // Count existing landlords
        $existingLandlordCount = User::whereHas('roles', function($query) {
            $query->where('role_name', 'landlord');
        })->where('username', 'like', 'landlord%')->count();
        
        $this->command->info("Found {$existingLandlordCount} existing landlords");
        
        // Create additional landlords until we have at least 5
        $landlordsToCreate = max(0, 5 - $existingLandlordCount);
        $this->command->info("Creating {$landlordsToCreate} additional landlords");
        
        $landlords = [];
        for ($i = $existingLandlordCount + 1; $i <= 5; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            $user = User::create([
                'username' => 'landlord' . $i,
                'email' => strtolower($firstName) . '.' . strtolower($lastName) . '@example.com',
                'password_hash' => Hash::make('password'),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => '+855 ' . rand(10, 19) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'status' => 'active',
            ]);
            
            // Attach landlord role
            $user->roles()->attach($landlordRole->role_id);
            
            $landlords[] = $user;
            $this->command->info("Created landlord {$i}: {$firstName} {$lastName}");
            
            // Create 2-3 properties for this landlord
            $propertyCount = rand(2, 3);
            for ($j = 1; $j <= $propertyCount; $j++) {
                $property = Property::create([
                    'landlord_id' => $user->user_id,
                    'property_name' => $faker->company . ' Residence',
                    'property_type' => $faker->randomElement(['Apartment', 'House', 'Condo', 'Duplex']),
                    'house_building_number' => $faker->buildingNumber,
                    'street' => $faker->streetName,
                    'village' => $faker->citySuffix,
                    'commune' => $faker->city,
                    'district' => $faker->state,
                    'total_floors' => rand(2, 5),
                    'total_rooms' => rand(5, 15),
                    'description' => $faker->paragraph,
                    'status' => 'active',
                    'year_built' => rand(2000, 2023),
                    'property_size' => rand(100, 500),
                    'size_measurement' => 'sqm',
                    'is_pets_allowed' => $faker->boolean,
                    'amenities' => json_encode(['wifi', 'parking', 'security']),
                ]);
                
                // Create 3-6 units for this property
                $unitCount = rand(3, 6);
                for ($k = 1; $k <= $unitCount; $k++) {
                    // Set due date to a random day of the current month
                    $dueDay = rand(1, 28);
                    $dueDate = \Carbon\Carbon::now()->setDay($dueDay)->startOfDay();
                    
                    Unit::create([
                        'property_id' => $property->property_id,
                        'room_number' => 'Unit ' . $k,
                        'room_name' => $faker->randomElement(['Studio', '1BR', '2BR', 'Deluxe', 'Standard', 'Premium']) . ' ' . $k,
                        'floor_number' => rand(1, $property->total_floors),
                        'room_type' => $faker->randomElement(['studio', 'one_bedroom', 'two_bedroom', 'three_bedroom']),
                        'type' => $faker->randomElement(['studio', 'one_bedroom', 'two_bedroom', 'three_bedroom']),
                        'description' => $faker->sentence,
                        'available' => true,
                        'status' => 'vacant',
                        'rent_amount' => rand(300, 1500),
                        'due_date' => $dueDate,
                    ]);
                }
                
                $this->command->info("  Created property {$property->property_name} with {$unitCount} units");
            }
        }
        
        $this->command->info('Created ' . count($landlords) . ' additional landlords with properties and units.');
    }
} 