<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_prices', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('utility_id')->references('utility_id')->on('utilities')->onDelete('cascade');

            // Optional per-property pricing override (null = global default for the utility)
            $table->foreignId('property_id')->nullable()->references('property_id')->on('property_details')->onDelete('cascade');

            $table->decimal('price', 10, 4);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->timestamps();

            $table->index(['utility_id', 'effective_from'], 'idx_utility_prices_effective');
            $table->index(['property_id', 'utility_id'], 'idx_utility_prices_property');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_prices');
    }
};
