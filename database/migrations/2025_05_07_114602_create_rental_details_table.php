<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Unified rental + lease agreement table.
 * Replaces the old separate lease_agreements and rental_details tables —
 * one tenancy = one row, with both contract metadata and active rental state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_details', function (Blueprint $table) {
            $table->id('rental_id');
            $table->foreignId('landlord_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('room_id')->references('room_id')->on('room_details')->onDelete('cascade');

            // Tenancy dates
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Financial terms (folded in from lease_agreements)
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();

            // Contract / agreement (folded in from lease_agreements)
            $table->string('agreement_file_path')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('signed_by_tenant')->default(false);
            $table->boolean('signed_by_landlord')->default(false);
            $table->timestamp('signed_at')->nullable();

            // Backwards-compat: existing code refers to `lease_agreement` as a path string
            $table->string('lease_agreement')->nullable();

            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['landlord_id', 'status'], 'idx_rentals_landlord_status');
            $table->index(['tenant_id', 'status'], 'idx_rentals_tenant_status');
            $table->index(['room_id', 'status'], 'idx_rentals_room_status');
            $table->index(['start_date', 'end_date'], 'idx_rentals_dates');
            $table->index(['end_date', 'status'], 'idx_rentals_expiring');
            $table->index('created_at', 'idx_rentals_created');
        });

        // Deferred FK: maintenance_requests.rental_id — column was created
        // earlier (without FK) because rental_details didn't exist yet.
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->foreign('rental_id')
                  ->references('rental_id')
                  ->on('rental_details')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropForeign(['rental_id']);
        });
        Schema::dropIfExists('rental_details');
    }
};
