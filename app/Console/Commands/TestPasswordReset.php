<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class TestPasswordReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:test-reset {email? : Email of the user to test} {--create : Create a new token} {--fix : Fix token issues} {--clear : Clear existing tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and diagnose password reset token functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if password_resets table exists
        if (!$this->checkResetTable()) {
            return 1;
        }

        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('Enter the email address to test');
        }
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }
        
        $this->info("Found user: {$user->email} (ID: {$user->user_id})");
        
        // Check for existing tokens
        $tokens = DB::table('password_resets')->where('email', $email)->get();
        
        if ($tokens->isEmpty()) {
            $this->warn("No password reset tokens found for {$email}");
            
            if ($this->option('create') || $this->confirm('Would you like to create a new token?')) {
                $this->createToken($email);
            }
            
            return 0;
        }
        
        // Display existing tokens
        $this->info("Found " . $tokens->count() . " token(s) for {$email}:");
        
        foreach ($tokens as $index => $record) {
            $this->line("Token #" . ($index + 1) . ":");
            $this->line("  Token: " . substr($record->token, 0, 20) . "...");
            $this->line("  Created: " . $record->created_at);
            
            // Check token expiry
            $created = new \DateTime($record->created_at);
            $now = new \DateTime();
            $diff = $now->diff($created);
            
            if ($diff->days > 0) {
                $this->error("  Status: EXPIRED (Created {$diff->days} days ago)");
            } else if ($diff->h > 1) {
                $this->warn("  Status: EXPIRING SOON (Created {$diff->h} hours ago)");
            } else {
                $this->info("  Status: VALID (Created {$diff->h} hours, {$diff->i} minutes ago)");
            }
        }
        
        // Clear tokens if requested
        if ($this->option('clear') || $this->confirm('Would you like to clear existing tokens?')) {
            DB::table('password_resets')->where('email', $email)->delete();
            $this->info("All tokens for {$email} have been cleared.");
        }
        
        // Create a new token if requested
        if ($this->option('create') || $this->confirm('Would you like to create a new token?')) {
            $this->createToken($email);
        }
        
        // Fix token issues if requested
        if ($this->option('fix')) {
            $this->fixTokenIssues($email);
        }
        
        return 0;
    }
    
    /**
     * Check if password_resets table exists and create it if needed
     */
    private function checkResetTable()
    {
        if (!DB::getSchemaBuilder()->hasTable('password_resets')) {
            $this->error("The password_resets table does not exist!");
            
            if ($this->confirm('Would you like to create the password_resets table?')) {
                $this->info("Creating password_resets table...");
                
                DB::statement("
                    CREATE TABLE password_resets (
                        email VARCHAR(255) NOT NULL,
                        token VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP NULL,
                        PRIMARY KEY (email)
                    )
                ");
                
                $this->info("Table created successfully!");
                return true;
            }
            
            return false;
        }
        
        $this->info("Password reset table exists.");
        
        // Check table columns
        $columns = DB::getSchemaBuilder()->getColumnListing('password_resets');
        $this->info("Table columns: " . implode(', ', $columns));
        
        return true;
    }
    
    /**
     * Create a new token for the user
     */
    private function createToken($email)
    {
        // First, delete any existing tokens
        DB::table('password_resets')->where('email', $email)->delete();
        
        // Generate a token
        $token = Str::random(64);
        
        // Store the token
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token, 
            'created_at' => now()
        ]);
        
        $this->info("Created new token for {$email}");
        $this->info("Token: {$token}");
        
        // Generate reset URL
        $resetUrl = url('reset-password') . '?token=' . $token . '&email=' . urlencode($email);
        $this->info("Reset URL: {$resetUrl}");
        
        return $token;
    }
    
    /**
     * Fix common token issues
     */
    private function fixTokenIssues($email)
    {
        $this->info("Fixing token issues for {$email}...");
        
        // Check table structure
        $tableInfo = DB::select("SHOW COLUMNS FROM password_resets");
        $columnNames = collect($tableInfo)->pluck('Field')->toArray();
        
        $this->info("Table structure: " . implode(', ', $columnNames));
        
        // Check configuration
        $configTable = config('auth.passwords.users.table');
        $this->info("Configured token table in auth.php: {$configTable}");
        
        // Fix token format if needed
        $tokens = DB::table('password_resets')->where('email', $email)->get();
        
        foreach ($tokens as $record) {
            if (strlen($record->token) < 60) {
                $this->warn("Token might be too short: " . strlen($record->token) . " chars");
                
                if ($this->confirm('Would you like to regenerate this token?')) {
                    $this->createToken($email);
                }
            }
        }
        
        $this->info("Token issues fixed.");
    }
} 