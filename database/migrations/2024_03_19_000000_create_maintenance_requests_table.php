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
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('tenant_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('property_details', 'property_id')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('room_details', 'room_id')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, completed, rejected
            $table->text('landlord_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
