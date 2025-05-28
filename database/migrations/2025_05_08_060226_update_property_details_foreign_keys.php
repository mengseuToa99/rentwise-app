<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the existing foreign key if it exists
        $constraints = DB::select("
            SELECT tc.constraint_name 
            FROM information_schema.table_constraints tc 
            JOIN information_schema.key_column_usage kcu
            ON tc.constraint_name = kcu.constraint_name
            WHERE tc.table_name = 'property_details' 
            AND kcu.column_name = 'landlord_id' 
            AND tc.constraint_type = 'FOREIGN KEY'
        ");

        foreach ($constraints as $constraint) {
            Schema::table('property_details', function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint->constraint_name);
            });
        }

        // Add the new foreign key
        Schema::table('property_details', function (Blueprint $table) {
            $table->foreign('landlord_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_details', function (Blueprint $table) {
            $table->dropForeign(['landlord_id']);
        });
    }
};
