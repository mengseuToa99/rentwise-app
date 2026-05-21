<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('tenant_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('property_details', 'property_id')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('room_details', 'room_id')->onDelete('cascade');
            // Links the request to the specific tenancy that raised it — preserves
            // history when the room turns over to a new tenant. FK added later
            // by the rental_details migration (table doesn't exist yet here).
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('landlord_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status'], 'idx_maintenance_property_status');
            $table->index(['tenant_id', 'status'], 'idx_maintenance_tenant_status');
            $table->index(['landlord_id', 'status'], 'idx_maintenance_landlord_status');
            $table->index(['rental_id', 'status'], 'idx_maintenance_rental_status');
            $table->index(['priority', 'status'], 'idx_maintenance_priority_status');
            $table->index('created_at', 'idx_maintenance_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
