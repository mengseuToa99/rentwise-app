<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->foreignId('property_id')->references('property_id')->on('property_details')->onDelete('cascade');
            $table->string('group_name');
            $table->string('room_type');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->json('amenities')->nullable();

            // Versioning — supports rent increases / seasonal pricing
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['property_id', 'status'], 'idx_pricing_property_status');
            $table->index(['property_id', 'effective_from', 'effective_until'], 'idx_pricing_effective');
        });

        // Deferred FK: room_details.pricing_group_id was created earlier without an FK
        // because pricing_groups didn't exist yet. Wire it up now.
        Schema::table('room_details', function (Blueprint $table) {
            $table->foreign('pricing_group_id')
                  ->references('group_id')
                  ->on('pricing_groups')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('room_details', function (Blueprint $table) {
            $table->dropForeign(['pricing_group_id']);
        });
        Schema::dropIfExists('pricing_groups');
    }
};
