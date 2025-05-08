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
        Schema::table('rental_details', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('active')->after('lease_agreement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
