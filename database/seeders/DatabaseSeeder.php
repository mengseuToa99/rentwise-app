<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the role seeder first to ensure roles exist
        $this->call([
            RolesSeeder::class,
        ]);
        
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password_hash' => Hash::make('password'),
                'phone_number' => '123-456-7890',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'status' => 'active',
            ]
        );
        
        // Attach admin role to the user
        $adminRole = Role::where('role_name', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->role_id]);
        }
        
        // Run additional seeders in dependency order
        $this->call([
            SystemSettingSeeder::class,  // No dependencies
            PermissionGroupSeeder::class, // Should run before PermissionSeeder
            PermissionSeeder::class,     // Depends on PermissionGroups
            LogSeeder::class,            // Depends on Users with roles
            PropertySeeder::class,       // Depends on Users with roles
            LandlordTenantSeeder::class, // Our new seeder for landlords, tenants, properties, and units
            MoreLandlordsSeeder::class,  // Add more landlords to reach at least 5
            MoreTenantsSeeder::class,    // Add more tenants to reach at least 20
            RentalAssignmentSeeder::class, // Assign rentals to actual tenants
        ]);
    }
}
