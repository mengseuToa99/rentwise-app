<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Make sure the roles table exists and has the admin role
        if (Schema::hasTable('roles')) {
            $adminRoleId = DB::table('roles')->where('role_name', 'admin')->value('role_id');
            
            if (!$adminRoleId) {
                $adminRoleId = DB::table('roles')->insertGetId([
                    'role_name' => 'admin',
                    'description' => 'System administrator',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info('Created admin role with ID: ' . $adminRoleId);
            } else {
                $this->command->info('Admin role already exists with ID: ' . $adminRoleId);
            }
            
            // 2. Make sure the user_details table exists and has an admin user
            if (Schema::hasTable('user_details')) {
                $adminUserId = DB::table('user_details')->where('email', 'admin@example.com')->value('user_id');
                
                if (!$adminUserId) {
                    $adminUserId = DB::table('user_details')->insertGetId([
                        'username' => 'admin',
                        'password_hash' => Hash::make('password'),
                        'email' => 'admin@example.com',
                        'first_name' => 'Admin',
                        'last_name' => 'User',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->command->info('Created admin user with ID: ' . $adminUserId);
                } else {
                    $this->command->info('Admin user already exists with ID: ' . $adminUserId);
                }
                
                // 3. Make sure the admin user has the admin role
                if (Schema::hasTable('user_roles')) {
                    $hasRole = DB::table('user_roles')
                        ->where('user_id', $adminUserId)
                        ->where('role_id', $adminRoleId)
                        ->exists();
                        
                    if (!$hasRole) {
                        DB::table('user_roles')->insert([
                            'user_id' => $adminUserId,
                            'role_id' => $adminRoleId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $this->command->info('Assigned admin role to admin user');
                    } else {
                        $this->command->info('Admin user already has admin role');
                    }
                } else {
                    $this->command->error('user_roles table does not exist');
                }
            } else {
                $this->command->error('user_details table does not exist');
            }
        } else {
            $this->command->error('roles table does not exist');
        }
    }
} 