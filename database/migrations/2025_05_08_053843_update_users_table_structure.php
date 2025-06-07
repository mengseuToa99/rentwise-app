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
        // First, drop foreign keys that reference the users table
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // Now we can safely drop and recreate the users table
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username')->unique();
            $table->string('password_hash');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('id_card_picture')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('last_login')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->string('first_name');
            $table->string('last_name');
            $table->timestamps();
            $table->rememberToken(); // Adding remember_token for Laravel authentication
        });

        // Recreate the foreign key constraints
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop foreign keys that reference the users table
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        // Now we can safely drop and recreate the users table
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Recreate the original foreign key constraints
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
