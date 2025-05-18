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
        Schema::table('property_details', function (Blueprint $table) {
            $table->string('property_type')->default('residential')->after('total_rooms');
            $table->integer('year_built')->nullable()->after('property_type');
            $table->decimal('property_size', 10, 2)->nullable()->after('year_built');
            $table->string('size_measurement')->default('sqft')->after('property_size');
            $table->json('amenities')->nullable()->after('size_measurement');
            $table->boolean('is_pets_allowed')->default(false)->after('amenities');
            $table->json('images')->nullable()->after('is_pets_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            $table->dropColumn([
                'property_type',
                'year_built',
                'property_size',
                'size_measurement',
                'amenities',
                'is_pets_allowed',
                'images'
            ]);
        });
    }
};
