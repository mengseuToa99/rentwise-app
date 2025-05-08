<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleDiagnostic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:diagnose {email? : The email of the user to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose role assignment issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting role diagnostics...');
        
        // Check tables
        $this->checkTables();
        
        // Check user
        $email = $this->argument('email') ?? 'admin@example.com';
        $user = DB::table('user_details')->where('email', $email)->first();
        
        if ($user) {
            $this->info("Found user with ID: {$user->user_id}");
            $this->checkUserRoles($user->user_id);
        } else {
            $this->error("User with email {$email} not found");
        }
        
        $this->info('Role diagnostics completed.');
    }
    
    private function checkTables()
    {
        $tables = ['roles', 'user_details', 'user_roles'];
        
        $this->info('Checking tables:');
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->info("  ✅ Table {$table} exists with {$count} records");
                
                if ($count > 0) {
                    $this->showSampleRecords($table);
                }
            } else {
                $this->error("  ❌ Table {$table} does not exist");
            }
        }
    }
    
    private function showSampleRecords($table)
    {
        $records = DB::table($table)->limit(3)->get();
        
        foreach ($records as $index => $record) {
            $this->info("    Record #{$index}: " . json_encode($record));
        }
    }
    
    private function checkUserRoles($userId)
    {
        $this->info("Checking roles for user ID: {$userId}");
        
        // Get roles directly from database
        $userRoles = DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->select('roles.role_id', 'roles.role_name', 'roles.description')
            ->get();
            
        if ($userRoles->count() > 0) {
            $this->info("  User has {$userRoles->count()} roles:");
            foreach ($userRoles as $role) {
                $this->info("    - {$role->role_name} (ID: {$role->role_id})");
            }
        } else {
            $this->error("  User has no roles assigned");
        }
    }
} 