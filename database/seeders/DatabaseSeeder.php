<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Rental;
use App\Models\MaintenanceRequest;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data safely
        DB::statement('TRUNCATE TABLE user_roles CASCADE');
        DB::statement('TRUNCATE TABLE roles CASCADE');
        DB::statement('TRUNCATE TABLE users CASCADE');
        DB::statement('TRUNCATE TABLE property_details CASCADE');
        DB::statement('TRUNCATE TABLE room_details CASCADE');
        DB::statement('TRUNCATE TABLE rental_details CASCADE');
        DB::statement('TRUNCATE TABLE maintenance_requests CASCADE');

        // Create basic roles
        $roles = [
            ['role_name' => 'admin', 'description' => 'System Administrator'],
            ['role_name' => 'landlord', 'description' => 'Property Owner'],
            ['role_name' => 'tenant', 'description' => 'Property Renter'],
            ['role_name' => 'agent', 'description' => 'Property Agent'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create admin user
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('password'),
            'phone_number' => '123-456-7890',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'status' => 'active',
        ]);

        // Attach admin role
        $adminRole = Role::where('role_name', 'admin')->first();
        $admin->roles()->attach($adminRole->role_id);

        // Create test landlord
        $landlord = User::create([
            'username' => 'landlord',
            'email' => 'landlord@example.com',
            'password_hash' => Hash::make('password'),
            'phone_number' => '123-456-7891',
            'first_name' => 'Test',
            'last_name' => 'Landlord',
            'status' => 'active',
        ]);

        // Attach landlord role
        $landlordRole = Role::where('role_name', 'landlord')->first();
        $landlord->roles()->attach($landlordRole->role_id);

        // Create test tenant
        $tenant = User::create([
            'username' => 'tenant',
            'email' => 'tenant@example.com',
            'password_hash' => Hash::make('password'),
            'phone_number' => '123-456-7892',
            'first_name' => 'Test',
            'last_name' => 'Tenant',
            'status' => 'active',
        ]);

        // Attach tenant role
        $tenantRole = Role::where('role_name', 'tenant')->first();
        $tenant->roles()->attach($tenantRole->role_id);

        // Create a test property
        $property = Property::create([
            'landlord_id' => $landlord->user_id,
            'property_name' => 'Test Property',
            'house_building_number' => '123',
            'street' => 'Test Street',
            'village' => 'Test Village',
            'commune' => 'Test Commune',
            'district' => 'Test District',
            'total_floors' => 2,
            'total_rooms' => 4,
            'description' => 'A test property',
            'status' => 'active',
        ]);

        // Create test units
        $unit = Unit::create([
            'property_id' => $property->property_id,
            'room_number' => 'R101',
            'floor_number' => 1,
            'room_name' => 'Test Room',
            'room_type' => 'one_bedroom',
            'description' => 'A test room',
            'available' => true,
            'rent_amount' => 500,
            'due_date' => Carbon::now()->addMonth(),
        ]);

        // Create test rental
        $rental = DB::table('rental_details')->insert([
            'landlord_id' => $landlord->user_id,
            'tenant_id' => $tenant->user_id,
            'room_id' => $unit->room_id,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYear(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create test maintenance request
        DB::table('maintenance_requests')->insert([
            'tenant_id' => $tenant->user_id,
            'property_id' => $property->property_id,
            'room_id' => $unit->room_id,
            'title' => 'Test Maintenance Request',
            'description' => 'This is a test maintenance request for the air conditioning unit.',
            'priority' => 'medium',
            'status' => 'pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Run essential system seeders
        $this->call([
            SystemSettingSeeder::class,
            PermissionGroupSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
