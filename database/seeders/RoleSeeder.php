<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_name' => 'admin',
                'description' => 'Administrator with full system access',
                'parent_role_id' => null
            ],
            [
                'role_name' => 'landlord',
                'description' => 'Property owner who can manage their own properties',
                'parent_role_id' => null
            ],
            [
                'role_name' => 'tenant',
                'description' => 'Property renter with limited access',
                'parent_role_id' => null
            ],
            [
                'role_name' => 'guest',
                'description' => 'Visitor with very limited access',
                'parent_role_id' => null
            ]
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['role_name' => $roleData['role_name']],
                $roleData
            );
        }
    }
}
