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
            // The address columns already exist, so we don't need to add them again
            // We're just keeping this migration for completeness
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert anything as we didn't make changes
    }
};
