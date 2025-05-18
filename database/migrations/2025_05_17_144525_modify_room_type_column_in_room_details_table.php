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
        Schema::table('room_details', function (Blueprint $table) {
            // Make room_type nullable
            if (Schema::hasColumn('room_details', 'room_type')) {
                $table->string('room_type')->nullable()->change();
            } else {
                // If the column doesn't exist yet, add it as nullable
                $table->string('room_type')->nullable()->after('floor_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_details', function (Blueprint $table) {
            // Revert room_type to be required (not nullable)
            if (Schema::hasColumn('room_details', 'room_type')) {
                $table->string('room_type')->nullable(false)->change();
            }
        });
    }
};
