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
        Schema::create('invoice_utility_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('usage_id');
            $table->timestamps();

            $table->foreign('invoice_id')->references('invoice_id')->on('invoice_details')->onDelete('cascade');
            $table->foreign('usage_id')->references('usage_id')->on('utility_usages')->onDelete('cascade');
            
            // Each utility usage can only be linked to one invoice
            $table->unique('usage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_utility_usages');
    }
}; 