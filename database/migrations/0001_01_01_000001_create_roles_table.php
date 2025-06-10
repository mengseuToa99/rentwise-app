<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name')->unique();
            $table->string('description')->nullable();
            $table->foreignId('parent_role_id')->nullable()->references('role_id')->on('roles')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->string('group_name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('access_permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('permission_name');
            $table->string('description')->nullable();
            $table->foreignId('group_id')->references('group_id')->on('permission_groups')->onDelete('cascade');
            $table->foreignId('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['permission_name', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_permissions');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
    }
}; 