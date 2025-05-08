<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserDetail;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Transfer data from user_details to users
        $userDetails = DB::table('user_details')->get();
        
        foreach ($userDetails as $userDetail) {
            // Check if a user with this email already exists in the new users table
            $existingUser = DB::table('users')->where('email', $userDetail->email)->first();
            
            if (!$existingUser) {
                // Insert user details into the new users table
                DB::table('users')->insert([
                    'user_id' => $userDetail->user_id,
                    'username' => $userDetail->username ?? ('user' . $userDetail->user_id),
                    'password_hash' => $userDetail->password_hash,
                    'email' => $userDetail->email,
                    'phone_number' => $userDetail->phone_number,
                    'profile_picture' => $userDetail->profile_picture,
                    'id_card_picture' => $userDetail->id_card_picture,
                    'status' => $userDetail->status,
                    'last_login' => $userDetail->last_login,
                    'failed_login_attempts' => $userDetail->failed_login_attempts,
                    'first_name' => $userDetail->first_name,
                    'last_name' => $userDetail->last_name,
                    'created_at' => $userDetail->created_at,
                    'updated_at' => $userDetail->updated_at,
                ]);
            }
        }
        
        // Update all foreign keys in related tables
        $tables = [
            'user_roles' => 'user_id',
            'rentals' => ['landlord_id', 'tenant_id'],
            'properties' => 'landlord_id',
            // Add other tables with user_id foreign keys as needed
        ];
        
        foreach ($tables as $table => $columns) {
            if (Schema::hasTable($table)) {
                $columns = is_array($columns) ? $columns : [$columns];
                
                foreach ($columns as $column) {
                    // No need to update since we're preserving the user_id values
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to do anything here, we're keeping user_details as is
    }
};
