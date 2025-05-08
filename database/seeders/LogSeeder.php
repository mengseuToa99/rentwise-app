<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user ID for logs
        $adminUser = User::whereHas('roles', function($query) {
            $query->where('role_name', 'admin');
        })->first();
        
        if (!$adminUser) {
            return;
        }
        
        $adminId = $adminUser->user_id;
        
        // Sample log entries
        $logs = [
            [
                'user_id' => $adminId,
                'action' => 'system_setup',
                'description' => 'Initial system setup completed',
                'timestamp' => Carbon::now()->subDays(30)
            ],
            [
                'user_id' => $adminId,
                'action' => 'create_role',
                'description' => 'Created default roles: admin, landlord, tenant',
                'timestamp' => Carbon::now()->subDays(29)
            ],
            [
                'user_id' => $adminId,
                'action' => 'create_permission_group',
                'description' => 'Created permission groups for system organization',
                'timestamp' => Carbon::now()->subDays(29)->addHours(1)
            ],
            [
                'user_id' => $adminId,
                'action' => 'create_permission',
                'description' => 'Assigned default permissions to admin role',
                'timestamp' => Carbon::now()->subDays(29)->addHours(2)
            ],
            [
                'user_id' => $adminId,
                'action' => 'update_setting',
                'description' => 'Updated system settings: site_name, currency, invoice_due_days',
                'timestamp' => Carbon::now()->subDays(28)
            ],
            [
                'user_id' => $adminId,
                'action' => 'login',
                'description' => 'Admin user logged in',
                'timestamp' => Carbon::now()->subDays(25)
            ],
            [
                'user_id' => $adminId,
                'action' => 'update_permission',
                'description' => 'Updated permissions for tenant role',
                'timestamp' => Carbon::now()->subDays(20)
            ],
            [
                'user_id' => $adminId,
                'action' => 'login',
                'description' => 'Admin user logged in',
                'timestamp' => Carbon::now()->subDays(15)
            ],
            [
                'user_id' => $adminId,
                'action' => 'system_maintenance',
                'description' => 'Performed routine system maintenance',
                'timestamp' => Carbon::now()->subDays(10)
            ],
            [
                'user_id' => $adminId,
                'action' => 'login',
                'description' => 'Admin user logged in',
                'timestamp' => Carbon::now()->subDays(5)
            ],
            [
                'user_id' => $adminId,
                'action' => 'create_setting',
                'description' => 'Added new system setting: maintenance_email',
                'timestamp' => Carbon::now()->subDays(3)
            ],
            [
                'user_id' => $adminId,
                'action' => 'login',
                'description' => 'Admin user logged in',
                'timestamp' => Carbon::now()->subDays(1)
            ],
            [
                'user_id' => $adminId,
                'action' => 'system_update',
                'description' => 'Updated system to latest version',
                'timestamp' => Carbon::now()->subHours(12)
            ],
        ];
        
        foreach ($logs as $log) {
            Log::create($log);
        }
    }
} 