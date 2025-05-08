<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add missing columns from UserDetail to the users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable();
            }
            if (!Schema::hasColumn('users', 'id_card_picture')) {
                $table->string('id_card_picture')->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0);
            }
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            // Rename password to password_hash if it exists
            if (Schema::hasColumn('users', 'password') && !Schema::hasColumn('users', 'password_hash')) {
                $table->renameColumn('password', 'password_hash');
            }
        });

        // Copy data from user_details to users
        if (Schema::hasTable('user_details')) {
            $userDetails = DB::table('user_details')->get();
            
            foreach ($userDetails as $detail) {
                // Check if user already exists
                $existingUser = DB::table('users')->where('email', $detail->email)->first();
                
                if ($existingUser) {
                    // Update existing user
                    DB::table('users')
                        ->where('email', $detail->email)
                        ->update([
                            'username' => $detail->username,
                            'phone_number' => $detail->phone_number,
                            'profile_picture' => $detail->profile_picture,
                            'id_card_picture' => $detail->id_card_picture,
                            'status' => $detail->status,
                            'failed_login_attempts' => $detail->failed_login_attempts,
                            'first_name' => $detail->first_name,
                            'last_name' => $detail->last_name,
                            'password_hash' => $detail->password_hash,
                        ]);
                } else {
                    // Create new user
                    DB::table('users')->insert([
                        'user_id' => $detail->user_id,
                        'username' => $detail->username,
                        'email' => $detail->email,
                        'phone_number' => $detail->phone_number,
                        'profile_picture' => $detail->profile_picture,
                        'id_card_picture' => $detail->id_card_picture,
                        'status' => $detail->status,
                        'failed_login_attempts' => $detail->failed_login_attempts,
                        'first_name' => $detail->first_name,
                        'last_name' => $detail->last_name,
                        'password_hash' => $detail->password_hash,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Update user_roles table to ensure it references users table correctly
        if (Schema::hasTable('user_roles')) {
            try {
                // Drop existing foreign keys if any
                Schema::table('user_roles', function (Blueprint $table) {
                    // Get all foreign keys
                    $foreignKeys = Schema::getConnection()
                        ->getDoctrineSchemaManager()
                        ->listTableForeignKeys('user_roles');
                    
                    if ($foreignKeys) {
                        foreach ($foreignKeys as $key) {
                            if (in_array('user_id', $key->getLocalColumns())) {
                                $table->dropForeign($key->getName());
                            }
                        }
                    }
                    
                    // Add the foreign key again
                    $table->foreign('user_id')
                          ->references('user_id')
                          ->on('users')
                          ->cascadeOnDelete();
                });
            } catch (\Exception $e) {
                // If we can't get foreign keys using Doctrine, try manually
                // This is a simplified approach that assumes standard Laravel naming
                Schema::table('user_roles', function (Blueprint $table) {
                    try {
                        $table->dropForeign(['user_id']);
                    } catch (\Exception $e) {
                        // Foreign key might not exist, continue
                    }
                    
                    try {
                        $table->foreign('user_id')
                              ->references('user_id')
                              ->on('users')
                              ->cascadeOnDelete();
                    } catch (\Exception $e) {
                        // If we can't add the foreign key, log it but continue
                        \Illuminate\Support\Facades\Log::warning('Could not add foreign key: ' . $e->getMessage());
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is not easily reversible as it involves data migration
        // and potentially breaking relationships
    }
};
