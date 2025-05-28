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
        // Update landlord_id foreign key
        $this->updateForeignKey('landlord_id');
        
        // Update tenant_id foreign key
        $this->updateForeignKey('tenant_id');
    }

    /**
     * Update a specific foreign key from user_details to users
     */
    private function updateForeignKey($column)
    {
        // First, drop the existing foreign key if it exists
        $constraints = DB::select("
            SELECT tc.constraint_name 
            FROM information_schema.table_constraints tc 
            JOIN information_schema.key_column_usage kcu
            ON tc.constraint_name = kcu.constraint_name
            WHERE tc.table_name = 'rental_details' 
            AND kcu.column_name = ?
            AND tc.constraint_type = 'FOREIGN KEY'
        ", [$column]);

        foreach ($constraints as $constraint) {
            Schema::table('rental_details', function (Blueprint $table) use ($constraint) {
                $table->dropForeign($constraint->constraint_name);
            });
        }

        // Add the new foreign key
        Schema::table('rental_details', function (Blueprint $table) use ($column) {
            $table->foreign($column)
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
        // Reverse landlord_id foreign key
        $this->reverseForeignKey('landlord_id');
        
        // Reverse tenant_id foreign key
        $this->reverseForeignKey('tenant_id');
    }

    /**
     * Reverse a specific foreign key back to user_details
     */
    private function reverseForeignKey($columnName)
    {
        // Drop the new foreign key constraint
        Schema::table('rental_details', function (Blueprint $table) use ($columnName) {
            $table->dropForeign([$columnName]);
        });

        // Add back the original foreign key constraint pointing to user_details
        Schema::table('rental_details', function (Blueprint $table) use ($columnName) {
            $table->foreign($columnName)
                  ->references('user_id')
                  ->on('user_details')
                  ->onDelete('cascade');
        });
    }
};
