<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_details', function (Blueprint $table) {
            $table->id('room_id');
            $table->foreignId('property_id')->references('property_id')->on('property_details')->onDelete('cascade');

            // FK to pricing_groups added later (table doesn't exist yet at this migration order)
            $table->unsignedBigInteger('pricing_group_id')->nullable();

            $table->string('room_number');
            $table->string('room_name')->nullable();
            $table->integer('floor_number');

            // Both 'type' and 'room_type' kept for backwards-compat with existing code
            $table->string('type')->nullable();
            $table->string('room_type')->nullable();

            $table->text('description')->nullable();
            $table->boolean('available')->default(true);
            $table->enum('status', ['vacant', 'occupied', 'maintenance', 'reserved'])->default('vacant');
            $table->decimal('rent_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status'], 'idx_rooms_property_status');
            $table->index(['status', 'available'], 'idx_rooms_status_available');
            $table->index('room_number', 'idx_rooms_number');
            $table->index(['property_id', 'room_number'], 'idx_rooms_property_number');
            $table->index('pricing_group_id', 'idx_rooms_pricing_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_details');
    }
};
