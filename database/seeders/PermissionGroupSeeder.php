<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PermissionGroup;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['group_name' => 'User Management', 'description' => 'Permissions related to user accounts and profiles'],
            ['group_name' => 'Property Management', 'description' => 'Permissions related to property operations'],
            ['group_name' => 'Rental Management', 'description' => 'Permissions related to rental contracts'],
            ['group_name' => 'Financial Management', 'description' => 'Permissions related to invoices and payments'],
            ['group_name' => 'System Configuration', 'description' => 'Permissions related to system settings'],
            ['group_name' => 'System Administration', 'description' => 'Core system admin permissions'],
            ['group_name' => 'Integration Management', 'description' => 'Permissions for third-party integrations'],
            ['group_name' => 'Maintenance Management', 'description' => 'Permissions for handling maintenance requests'],
            ['group_name' => 'Communication', 'description' => 'Permissions for messaging and notifications'],
            ['group_name' => 'Document Management', 'description' => 'Permissions for handling documents and files'],
            ['group_name' => 'Reporting', 'description' => 'Permissions for generating and viewing reports'],
        ];
        
        foreach($groups as $group) {
            PermissionGroup::updateOrCreate(
                ['group_name' => $group['group_name']],
                ['description' => $group['description']]
            );
        }
    }
} 