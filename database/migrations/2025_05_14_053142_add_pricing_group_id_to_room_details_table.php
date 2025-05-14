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
            $table->unsignedBigInteger('pricing_group_id')->nullable()->after('property_id');
            $table->foreign('pricing_group_id')->references('group_id')->on('pricing_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_details', function (Blueprint $table) {
            $table->dropForeign(['pricing_group_id']);
            $table->dropColumn('pricing_group_id');
        });
    }
};
