<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-password {email} {password} {--force : Force update without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a user\'s password directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $force = $this->option('force');
        
        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $this->info("Found user: {$email} (ID: {$user->user_id})");
        
        if (!$force) {
            // Show current password hash info
            if (!empty($user->password_hash)) {
                $this->info("Current password hash: " . substr($user->password_hash, 0, 10) . '...');
                $this->info("Current hash length: " . strlen($user->password_hash));
            } else {
                $this->warn("User has no password hash currently set.");
            }
            
            // Confirm
            if (!$this->confirm("Are you sure you want to set the password for {$email} to '{$password}'?")) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }
        
        // Update the password
        $hashedPassword = Hash::make($password);
        $user->password_hash = $hashedPassword;
        $user->save();
        
        $this->info("Password for user {$email} has been set.");
        $this->info("New hash: " . substr($hashedPassword, 0, 10) . '...');
        $this->info("New hash length: " . strlen($hashedPassword));
        
        // Verify the hash
        $isValid = Hash::check($password, $user->fresh()->password_hash);
        
        if ($isValid) {
            $this->info("Password hash has been verified successfully.");
        } else {
            $this->error("Password hash verification failed!");
            $this->warn("This means the user won't be able to log in with this password.");
            return 1;
        }
        
        return 0;
    }
}
