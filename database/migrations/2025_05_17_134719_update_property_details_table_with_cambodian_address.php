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
            // Drop the old address and location columns
            $table->dropColumn('address');
            $table->dropColumn('location');
            
            // Add new Cambodian address fields
            $table->string('house_building_number')->nullable()->after('property_name');
            $table->string('street')->nullable()->after('house_building_number');
            $table->string('village')->nullable()->after('street');
            $table->string('commune')->nullable()->after('village');
            $table->string('district')->nullable()->after('commune');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            // Drop the new Cambodian address fields
            $table->dropColumn('house_building_number');
            $table->dropColumn('street');
            $table->dropColumn('village');
            $table->dropColumn('commune');
            $table->dropColumn('district');
            
            // Re-add the old address and location columns
            $table->string('address')->after('property_name');
            $table->string('location')->after('address');
        });
    }
};
