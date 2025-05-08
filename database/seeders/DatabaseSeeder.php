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
    }
}
