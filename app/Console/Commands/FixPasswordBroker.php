<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class FixPasswordBroker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:fix-broker {--test-email= : Email to test the password reset token creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and fix Laravel password broker configuration issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Diagnosing Laravel password broker configuration...');
        
        // 1. Check auth configuration
        $this->checkAuthConfig();
        
        // 2. Check DB tables
        $this->checkTables();
        
        // 3. Test token creation if an email is provided
        $testEmail = $this->option('test-email');
        if ($testEmail) {
            $this->testTokenCreation($testEmail);
        }
        
        $this->info('Password broker diagnosis completed.');
        
        return 0;
    }
    
    private function checkAuthConfig()
    {
        $this->info("\nChecking auth configuration:");
        
        // Get the provider
        $provider = config('auth.defaults.passwords', 'users');
        $this->info("Default password broker: {$provider}");
        
        // Check provider configuration
        $providerConfig = config("auth.passwords.{$provider}");
        
        if (!$providerConfig) {
            $this->error("Password provider '{$provider}' not configured in auth.php!");
            $this->fixConfigIfNeeded();
            return;
        }
        
        $this->info("Provider configuration:");
        $this->info("  Table: " . $providerConfig['table']);
        $this->info("  Expire: " . $providerConfig['expire'] . " minutes");
        $this->info("  Throttle: " . $providerConfig['throttle'] . " seconds");
        
        // Check User model
        $userModel = User::class;
        $this->info("\nChecking User model:");
        
        // Check if it implements CanResetPassword
        $interfaces = class_implements($userModel);
        $canReset = in_array('Illuminate\Contracts\Auth\CanResetPassword', $interfaces);
        
        if (!$canReset) {
            $this->error("User model does not implement CanResetPassword interface!");
        } else {
            $this->info("✓ User model implements CanResetPassword interface");
        }
        
        // Check if it uses the CanResetPassword trait
        $reflection = new \ReflectionClass($userModel);
        $traits = $reflection->getTraitNames();
        $hasTrait = in_array('Illuminate\Auth\Passwords\CanResetPassword', $traits);
        
        if (!$hasTrait) {
            $this->error("User model does not use CanResetPassword trait!");
        } else {
            $this->info("✓ User model uses CanResetPassword trait");
        }
        
        // Check getEmailForPasswordReset method
        $user = new User();
        $email = $user->getEmailForPasswordReset();
        $this->info("Email field for password reset: " . ($email ?: 'Not available'));
        
        // Check password field accessors
        if (method_exists($user, 'getPasswordAttribute')) {
            $this->info("✓ User model has password getter");
        } else {
            $this->warn("User model does not have a getPasswordAttribute method");
        }
        
        if (method_exists($user, 'setPasswordAttribute')) {
            $this->info("✓ User model has password setter");
        } else {
            $this->warn("User model does not have a setPasswordAttribute method");
        }
    }
    
    private function checkTables()
    {
        $this->info("\nChecking database tables:");
        
        $tables = ['password_resets', 'password_reset_tokens'];
        $providedTable = config('auth.passwords.users.table');
        
        if (!in_array($providedTable, $tables)) {
            $tables[] = $providedTable;
        }
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✓ Table {$table} exists");
                
                // Check columns
                $columns = Schema::getColumnListing($table);
                $this->info("  Columns: " . implode(', ', $columns));
                
                // Check if it has data
                $count = DB::table($table)->count();
                $this->info("  Records: {$count}");
                
                if ($count > 0) {
                    $sample = DB::table($table)->first();
                    $this->info("  Sample record:");
                    foreach ((array)$sample as $key => $value) {
                        // Only show part of the token for security
                        if ($key === 'token' && strlen($value) > 10) {
                            $value = substr($value, 0, 10) . '...';
                        }
                        $this->info("    {$key}: {$value}");
                    }
                }
            } else {
                $this->error("✗ Table {$table} does not exist");
                
                if ($this->confirm("Would you like to create the {$table} table?")) {
                    Schema::create($table, function ($table) {
                        $table->string('email')->primary();
                        $table->string('token');
                        $table->timestamp('created_at')->nullable();
                    });
                    $this->info("Table {$table} created!");
                }
            }
        }
        
        // Check which table is configured in .env
        $envTable = env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens');
        $this->info("\nConfigured table in .env: " . $envTable);
    }
    
    private function testTokenCreation($email)
    {
        $this->info("\nTesting token creation for {$email}:");
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return;
        }
        
        $this->info("User found: " . $user->email);
        
        try {
            // Create token
            $broker = app('auth.password.broker');
            $token = $broker->createToken($user);
            
            $this->info("Token created: " . substr($token, 0, 10) . '...');
            $this->info("Token length: " . strlen($token));
            
            // Check if token was stored
            $table = config('auth.passwords.users.table');
            $record = DB::table($table)->where('email', $email)->first();
            
            if ($record) {
                $this->info("✓ Token record found in database");
                $this->info("  Created at: " . $record->created_at);
                $this->info("  Stored token (hashed): " . substr($record->token, 0, 10) . '...');
                
                // Check if token validates
                $validates = $broker->tokenExists($user, $token);
                
                if ($validates) {
                    $this->info("✓ Token validates correctly");
                } else {
                    $this->error("✗ Token does not validate!");
                    
                    // Try fixing it
                    if ($this->confirm("Would you like to recreate the token?")) {
                        // Delete old token
                        DB::table($table)->where('email', $email)->delete();
                        
                        // Create new token
                        $newToken = $broker->createToken($user);
                        
                        $this->info("New token created: " . substr($newToken, 0, 10) . '...');
                        
                        // Generate reset URL
                        $resetUrl = url('reset-password') . '?token=' . $newToken . '&email=' . urlencode($email);
                        $this->info("Reset URL: {$resetUrl}");
                    }
                }
            } else {
                $this->error("✗ No token record found in database!");
            }
        } catch (\Exception $e) {
            $this->error("Error creating token: " . $e->getMessage());
        }
    }
    
    private function fixConfigIfNeeded()
    {
        if ($this->confirm("Would you like to fix the auth configuration?")) {
            // Set default configuration
            config(['auth.passwords.users' => [
                'provider' => 'users',
                'table' => 'password_resets',
                'expire' => 60,
                'throttle' => 60,
            ]]);
            
            $this->info("Auth configuration updated in memory.");
            $this->info("You need to update your config/auth.php file manually with this configuration.");
        }
    }
} 