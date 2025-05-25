<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Faker\Factory as Faker;

class MoreTenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get tenant role
        $tenantRole = Role::where('role_name', 'tenant')->first();
        
        if (!$tenantRole) {
            $this->command->error('Tenant role not found!');
            return;
        }
        
        // Count existing tenants
        $existingTenantCount = User::whereHas('roles', function($query) {
            $query->where('role_name', 'tenant');
        })->where('username', 'like', 'tenant%')->count();
        
        $this->command->info("Found {$existingTenantCount} existing tenants");
        
        // Create additional tenants until we have at least 20
        $tenantsToCreate = max(0, 20 - $existingTenantCount);
        $this->command->info("Creating {$tenantsToCreate} additional tenants");
        
        $nationalities = ['Cambodian', 'Vietnamese', 'Thai', 'Chinese', 'Korean', 'Japanese', 'Filipino', 'American', 'British', 'French', 'German', 'Australian', 'Indian'];
        
        $tenants = [];
        for ($i = $existingTenantCount + 1; $i <= 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            $user = User::create([
                'username' => 'tenant' . $i,
                'email' => strtolower($firstName) . '.' . strtolower($lastName) . '@example.com',
                'password_hash' => Hash::make('password'),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_number' => '+855 ' . rand(10, 19) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'status' => 'active',
            ]);
            
            // Attach tenant role
            $user->roles()->attach($tenantRole->role_id);
            
            $tenants[] = $user;
            $this->command->info("Created tenant {$i}: {$firstName} {$lastName}");
        }
        
        $this->command->info('Created ' . count($tenants) . ' additional tenants.');
    }
} 