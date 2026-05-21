<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('rental_id')->references('rental_id')->on('rental_details')->onDelete('cascade');

            $table->string('invoice_number')->unique()->nullable();
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0); // running total of payments

            // Billing period (for recurring rent invoices)
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('due_date');

            // Single source of truth for status (no more redundant `paid` bool)
            $table->enum('payment_status', ['draft', 'pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['rental_id', 'payment_status'], 'idx_invoices_rental_status');
            $table->index(['payment_status', 'due_date'], 'idx_invoices_status_due');
            $table->index(['due_date', 'payment_status'], 'idx_invoices_due_status');
            $table->index('amount_due', 'idx_invoices_amount');
            $table->index('created_at', 'idx_invoices_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_details');
    }
};
