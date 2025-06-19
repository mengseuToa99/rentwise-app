<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PERFORMANCE: Add critical indexes for navigation optimization
 * 
 * These indexes will improve query performance for:
 * - Dashboard stats queries
 * - List component filtering and searching
 * - User role lookups
 * - Rental and invoice queries
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PERFORMANCE: User and role indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email', 'email_verified_at'], 'idx_users_email_verified');
            $table->index(['first_name', 'last_name'], 'idx_users_names');
            $table->index('phone_number', 'idx_users_phone');
            $table->index('created_at', 'idx_users_created');
        });
        
        Schema::table('user_roles', function (Blueprint $table) {
            $table->index(['user_id', 'role_id'], 'idx_user_roles_composite');
        });
        
        Schema::table('roles', function (Blueprint $table) {
            $table->index('role_name', 'idx_roles_name');
        });
        
        // PERFORMANCE: Property and unit indexes
        Schema::table('property_details', function (Blueprint $table) {
            $table->index('landlord_id', 'idx_properties_landlord');
            $table->index('property_name', 'idx_properties_name');
            $table->index(['landlord_id', 'property_name'], 'idx_properties_landlord_name');
            $table->index('created_at', 'idx_properties_created');
        });
        
        Schema::table('room_details', function (Blueprint $table) {
            $table->index(['property_id', 'status'], 'idx_rooms_property_status');
            $table->index(['status', 'available'], 'idx_rooms_status_available');
            $table->index('room_number', 'idx_rooms_number');
            $table->index(['property_id', 'room_number'], 'idx_rooms_property_number');
        });
        
        // PERFORMANCE: Rental indexes
        Schema::table('rental_details', function (Blueprint $table) {
            $table->index(['landlord_id', 'status'], 'idx_rentals_landlord_status');
            $table->index(['tenant_id', 'status'], 'idx_rentals_tenant_status');
            $table->index(['room_id', 'status'], 'idx_rentals_room_status');
            $table->index(['start_date', 'end_date'], 'idx_rentals_dates');
            $table->index(['end_date', 'status'], 'idx_rentals_expiring');
            $table->index('created_at', 'idx_rentals_created');
        });
        
        // PERFORMANCE: Invoice indexes
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->index(['rental_id', 'payment_status'], 'idx_invoices_rental_status');
            $table->index(['payment_status', 'due_date'], 'idx_invoices_status_due');
            $table->index(['due_date', 'payment_status'], 'idx_invoices_due_status');
            $table->index('amount_due', 'idx_invoices_amount');
            $table->index('created_at', 'idx_invoices_created');
        });
        
        // PERFORMANCE: Maintenance request indexes (if table exists)
        if (Schema::hasTable('maintenance_requests')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->index(['property_id', 'status'], 'idx_maintenance_property_status');
                $table->index(['tenant_id', 'status'], 'idx_maintenance_tenant_status');
                $table->index('created_at', 'idx_maintenance_created');
            });
        }
        
        // PERFORMANCE: Chat related indexes (if tables exist)
        if (Schema::hasTable('chat_rooms')) {
            Schema::table('chat_rooms', function (Blueprint $table) {
                $table->index('created_at', 'idx_chat_rooms_created');
                $table->index('updated_at', 'idx_chat_rooms_updated');
            });
        }
        
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->index(['chat_room_id', 'created_at'], 'idx_chat_messages_room_time');
                $table->index('user_id', 'idx_chat_messages_user');
            });
        }
        
        // PERFORMANCE: Utility usage indexes (if table exists)
        if (Schema::hasTable('utility_usage')) {
            Schema::table('utility_usage', function (Blueprint $table) {
                $table->index(['rental_id', 'usage_date'], 'idx_utility_rental_date');
                $table->index('usage_date', 'idx_utility_date');
            });
        }
        
        // PERFORMANCE: Add composite indexes for common dashboard queries
        if (Schema::hasTable('payment_history')) {
            Schema::table('payment_history', function (Blueprint $table) {
                $table->index(['rental_id', 'payment_date'], 'idx_payments_rental_date');
                $table->index('payment_date', 'idx_payments_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_email_verified');
            $table->dropIndex('idx_users_names');
            $table->dropIndex('idx_users_phone');
            $table->dropIndex('idx_users_created');
        });
        
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropIndex('idx_user_roles_composite');
        });
        
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('idx_roles_name');
        });
        
        Schema::table('property_details', function (Blueprint $table) {
            $table->dropIndex('idx_properties_landlord');
            $table->dropIndex('idx_properties_name');
            $table->dropIndex('idx_properties_landlord_name');
            $table->dropIndex('idx_properties_created');
        });
        
        Schema::table('room_details', function (Blueprint $table) {
            $table->dropIndex('idx_rooms_property_status');
            $table->dropIndex('idx_rooms_status_available');
            $table->dropIndex('idx_rooms_number');
            $table->dropIndex('idx_rooms_property_number');
        });
        
        Schema::table('rental_details', function (Blueprint $table) {
            $table->dropIndex('idx_rentals_landlord_status');
            $table->dropIndex('idx_rentals_tenant_status');
            $table->dropIndex('idx_rentals_room_status');
            $table->dropIndex('idx_rentals_dates');
            $table->dropIndex('idx_rentals_expiring');
            $table->dropIndex('idx_rentals_created');
        });
        
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_rental_status');
            $table->dropIndex('idx_invoices_status_due');
            $table->dropIndex('idx_invoices_due_status');
            $table->dropIndex('idx_invoices_amount');
            $table->dropIndex('idx_invoices_created');
        });
        
        if (Schema::hasTable('maintenance_requests')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->dropIndex('idx_maintenance_property_status');
                $table->dropIndex('idx_maintenance_tenant_status');
                $table->dropIndex('idx_maintenance_created');
            });
        }
        
        if (Schema::hasTable('chat_rooms')) {
            Schema::table('chat_rooms', function (Blueprint $table) {
                $table->dropIndex('idx_chat_rooms_created');
                $table->dropIndex('idx_chat_rooms_updated');
            });
        }
        
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropIndex('idx_chat_messages_room_time');
                $table->dropIndex('idx_chat_messages_user');
            });
        }
        
        if (Schema::hasTable('utility_usage')) {
            Schema::table('utility_usage', function (Blueprint $table) {
                $table->dropIndex('idx_utility_rental_date');
                $table->dropIndex('idx_utility_date');
            });
        }
        
        if (Schema::hasTable('payment_history')) {
            Schema::table('payment_history', function (Blueprint $table) {
                $table->dropIndex('idx_payments_rental_date');
                $table->dropIndex('idx_payments_date');
            });
        }
    }
}; 