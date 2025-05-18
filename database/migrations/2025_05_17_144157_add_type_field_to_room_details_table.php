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
            // Add the missing columns
            if (!Schema::hasColumn('room_details', 'type')) {
                $table->string('type')->nullable()->after('floor_number');
            }
            
            if (!Schema::hasColumn('room_details', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->default(0)->after('type');
            }
            
            if (!Schema::hasColumn('room_details', 'due_date')) {
                $table->date('due_date')->nullable()->after('rent_amount');
            }
            
            if (!Schema::hasColumn('room_details', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_details', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('room_details', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('room_details', 'rent_amount')) {
                $table->dropColumn('rent_amount');
            }
            
            if (Schema::hasColumn('room_details', 'due_date')) {
                $table->dropColumn('due_date');
            }
            
            if (Schema::hasColumn('room_details', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
