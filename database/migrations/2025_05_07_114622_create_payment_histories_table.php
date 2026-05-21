<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('invoice_id')->references('invoice_id')->on('invoice_details')->onDelete('cascade');
            $table->foreignId('recorded_by_user_id')->nullable()->references('user_id')->on('users')->nullOnDelete();

            $table->decimal('payment_amount', 10, 2);
            $table->timestamp('payment_date');

            // payment_method only lives here (removed from invoice to drop duplication)
            $table->enum('payment_method', ['cash', 'credit_card', 'bank_transfer', 'wing', 'aba', 'other'])->default('cash');

            $table->string('transaction_ref')->nullable();
            $table->string('receipt_number')->nullable();
            $table->enum('reconciliation_status', ['unverified', 'verified', 'disputed', 'refunded'])->default('unverified');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['invoice_id', 'payment_date'], 'idx_payments_invoice_date');
            $table->index('payment_date', 'idx_payments_date');
            $table->index('transaction_ref', 'idx_payments_txnref');
            $table->index('reconciliation_status', 'idx_payments_reconciliation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_histories');
    }
};
