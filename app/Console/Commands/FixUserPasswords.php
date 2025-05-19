<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user-passwords {email? : Check a specific user by email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix or diagnose user password issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            // Check one specific user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return 1;
            }
            
            $this->diagnoseUser($user);
            
            if ($this->confirm("Would you like to reset the password for {$email}?")) {
                $password = $this->secret("Enter the new password");
                
                if (empty($password)) {
                    $this->error("Password cannot be empty.");
                    return 1;
                }
                
                $user->password_hash = Hash::make($password);
                $user->save();
                
                $this->info("Password for {$email} has been reset.");
                
                // Verify the new password hash works
                if (Hash::check($password, $user->password_hash)) {
                    $this->info("✓ Password verification successful.");
                } else {
                    $this->error("✗ Password verification failed!");
                }
            }
        } else {
            // List all users and diagnose
            $users = User::all();
            
            $this->table(
                ['ID', 'Email', 'Hash Present', 'Hash Format OK', 'Hash Length'],
                $users->map(function ($user) {
                    $hashPresent = !empty($user->password_hash);
                    $hashFormatOk = $hashPresent && substr($user->password_hash, 0, 4) === '$2y$';
                    $hashLength = $hashPresent ? strlen($user->password_hash) : 0;
                    
                    return [
                        $user->user_id,
                        $user->email,
                        $hashPresent ? '✓' : '✗',
                        $hashFormatOk ? '✓' : '✗',
                        $hashLength
                    ];
                })
            );
            
            if ($this->confirm("Would you like to diagnose a specific user?")) {
                $email = $this->ask("Enter the user's email");
                $user = User::where('email', $email)->first();
                
                if (!$user) {
                    $this->error("User with email {$email} not found.");
                    return 1;
                }
                
                $this->diagnoseUser($user);
            }
        }
        
        return 0;
    }
    
    /**
     * Diagnose a user's password hash
     * 
     * @param User $user
     */
    private function diagnoseUser(User $user)
    {
        $this->info("Diagnosing user: {$user->email}");
        $this->line("User ID: {$user->user_id}");
        $this->line("Status: {$user->status}");
        
        if (empty($user->password_hash)) {
            $this->error("No password hash found!");
            return;
        }
        
        $this->line("Password hash: " . substr($user->password_hash, 0, 10) . '...');
        $this->line("Hash length: " . strlen($user->password_hash));
        
        $hashFormatOk = substr($user->password_hash, 0, 4) === '$2y$';
        if ($hashFormatOk) {
            $this->info("✓ Hash format looks correct (starts with \$2y\$)");
        } else {
            $this->error("✗ Hash format does not look correct (should start with \$2y\$)");
        }
        
        // Test login with a specific password
        if ($this->confirm("Would you like to test logging in with a specific password?")) {
            $testPassword = $this->secret("Enter the password to test");
            
            if (Hash::check($testPassword, $user->password_hash)) {
                $this->info("✓ Password verified successfully!");
            } else {
                $this->error("✗ Password verification failed!");
            }
        }
    }
}
