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
    private function updateForeignKey($columnName)
    {
        // First, get the constraint name
        $constraintName = '';
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'rental_details' 
            AND COLUMN_NAME = '{$columnName}' 
            AND REFERENCED_TABLE_NAME = 'user_details'
            AND CONSTRAINT_SCHEMA = DATABASE()
        ");

        if (!empty($constraints)) {
            $constraintName = $constraints[0]->CONSTRAINT_NAME;
        }

        // Drop the existing foreign key constraint
        if ($constraintName !== '') {
            Schema::table('rental_details', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
        } else {
            // If we couldn't get the name dynamically, try the standard Laravel naming convention
            Schema::table('rental_details', function (Blueprint $table) use ($columnName) {
                try {
                    $table->dropForeign(['rental_details_' . $columnName . '_foreign']);
                } catch (\Exception $e) {
                    // If this fails, log or output a message
                    echo "Could not drop foreign key for {$columnName} with standard name: " . $e->getMessage() . "\n";
                }
            });
        }

        // Add the new foreign key constraint pointing to the users table
        Schema::table('rental_details', function (Blueprint $table) use ($columnName) {
            $table->foreign($columnName)
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
