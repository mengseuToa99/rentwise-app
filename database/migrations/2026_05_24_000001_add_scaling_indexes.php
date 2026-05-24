<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->index(['role_id', 'user_id'], 'idx_user_roles_role_user');
        });

        Schema::table('rental_details', function (Blueprint $table) {
            $table->index(['landlord_id', 'tenant_id', 'status'], 'idx_rentals_landlord_tenant_status');
        });

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->index(['rental_id', 'due_date', 'payment_status'], 'idx_invoices_rental_due_status');
            $table->index(['payment_status', 'updated_at'], 'idx_invoices_status_updated');
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->index(['tenant_id', 'created_at'], 'idx_maintenance_tenant_created');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->index(['queue', 'reserved_at', 'available_at'], 'idx_jobs_queue_polling');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_queue_polling');
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropIndex('idx_maintenance_tenant_created');
        });

        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_status_updated');
            $table->dropIndex('idx_invoices_rental_due_status');
        });

        Schema::table('rental_details', function (Blueprint $table) {
            $table->dropIndex('idx_rentals_landlord_tenant_status');
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropIndex('idx_user_roles_role_user');
        });
    }
};
