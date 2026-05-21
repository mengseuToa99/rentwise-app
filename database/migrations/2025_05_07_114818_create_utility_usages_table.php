<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Utility metering structure.
 *
 * - utility_meters: a physical meter belonging to a property OR a specific room
 *   (room_id null = building/shared meter). allocation_method controls how a
 *   shared meter's usage is split across units.
 * - utility_usages: a meter reading. Crucially, every reading is tied to the
 *   specific rental period (rental_id) that was active when the reading was
 *   taken — so when a room changes hands, you can still say "Tenant A used
 *   450 kWh during their tenancy and Tenant B used 200 kWh during theirs".
 *   reading_type marks move-in / move-out readings so billing handoffs are
 *   unambiguous.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_meters', function (Blueprint $table) {
            $table->id('meter_id');
            $table->foreignId('property_id')->references('property_id')->on('property_details')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->references('room_id')->on('room_details')->onDelete('cascade');
            $table->foreignId('utility_id')->references('utility_id')->on('utilities')->onDelete('cascade');

            $table->string('meter_identifier')->nullable();
            $table->enum('allocation_method', ['per_room', 'equal_split', 'per_sqft', 'per_occupant', 'fixed'])
                  ->default('per_room');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['property_id', 'utility_id'], 'idx_meters_property_utility');
            $table->index(['room_id', 'is_active'], 'idx_meters_room_active');
        });

        Schema::create('utility_usages', function (Blueprint $table) {
            $table->id('usage_id');

            // Either tied to a meter (preferred) or directly to a room (legacy)
            $table->foreignId('meter_id')->nullable()->references('meter_id')->on('utility_meters')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->references('room_id')->on('room_details')->onDelete('cascade');
            $table->foreignId('utility_id')->references('utility_id')->on('utilities')->onDelete('cascade');

            // Tenancy attribution — links the reading to the rental that was
            // active when the reading was taken. Nullable so vacant-period
            // readings (between tenancies) can still be recorded.
            $table->foreignId('rental_id')->nullable()->references('rental_id')->on('rental_details')->nullOnDelete();

            // Who took the reading (landlord / staff / tenant self-report)
            $table->foreignId('recorded_by_user_id')->nullable()->references('user_id')->on('users')->nullOnDelete();

            // periodic = normal monthly reading, move_in / move_out = handover
            // readings used to split a billing period between two tenants,
            // adjustment = correction entry.
            $table->enum('reading_type', ['periodic', 'move_in', 'move_out', 'adjustment'])->default('periodic');

            $table->date('usage_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            $table->decimal('old_meter_reading', 12, 3);
            $table->decimal('new_meter_reading', 12, 3);
            $table->decimal('amount_used', 12, 3);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['meter_id', 'usage_date'], 'idx_usages_meter_date');
            $table->index(['room_id', 'usage_date'], 'idx_usages_room_date');
            $table->index(['rental_id', 'usage_date'], 'idx_usages_rental_date');
            $table->index('usage_date', 'idx_usages_date');
            $table->index('reading_type', 'idx_usages_reading_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_usages');
        Schema::dropIfExists('utility_meters');
    }
};
