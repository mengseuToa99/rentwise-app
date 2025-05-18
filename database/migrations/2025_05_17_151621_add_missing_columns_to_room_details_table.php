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
            if (!Schema::hasColumn('room_details', 'type')) {
                $table->string('type')->nullable()->after('room_number');
            }
            
            if (!Schema::hasColumn('room_details', 'room_name')) {
                $table->string('room_name')->nullable()->after('floor_number');
            }
            
            if (!Schema::hasColumn('room_details', 'description')) {
                $table->text('description')->nullable()->after('room_name');
            }
            
            if (!Schema::hasColumn('room_details', 'available')) {
                $table->boolean('available')->default(true)->after('description');
            }
            
            if (!Schema::hasColumn('room_details', 'status')) {
                $table->string('status')->default('vacant')->after('available');
            }
            
            if (!Schema::hasColumn('room_details', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('room_details', 'due_date')) {
                $table->date('due_date')->nullable()->after('rent_amount');
            }
            
            if (!Schema::hasColumn('room_details', 'pricing_group_id')) {
                $table->unsignedBigInteger('pricing_group_id')->nullable()->after('property_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_details', function (Blueprint $table) {
            $columns = [
                'type',
                'room_name',
                'description',
                'available',
                'status',
                'rent_amount',
                'due_date',
                'pricing_group_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('room_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
