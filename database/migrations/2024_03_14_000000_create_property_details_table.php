<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_details', function (Blueprint $table) {
            $table->id('property_id');
            $table->foreignId('landlord_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->string('property_name');

            // Cambodian address fields (consolidated)
            $table->string('house_building_number')->nullable();
            $table->string('building_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('street')->nullable();
            $table->string('village')->nullable();
            $table->string('commune')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();

            $table->integer('total_floors');
            $table->integer('total_rooms');

            // Property classification
            $table->enum('property_type', ['residential', 'commercial', 'industrial', 'mixed'])->nullable();
            $table->integer('year_built')->nullable();
            $table->decimal('property_size', 10, 2)->nullable();
            $table->string('size_measurement', 10)->default('sqft');
            $table->json('amenities')->nullable();
            $table->boolean('is_pets_allowed')->default(false);

            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('landlord_id', 'idx_properties_landlord');
            $table->index('property_name', 'idx_properties_name');
            $table->index(['landlord_id', 'property_name'], 'idx_properties_landlord_name');
            $table->index(['landlord_id', 'status'], 'idx_properties_landlord_status');
            $table->index('created_at', 'idx_properties_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_details');
    }
};
