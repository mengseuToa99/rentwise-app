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
        // First, get the constraint name
        $constraintName = '';
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'property_details' 
            AND COLUMN_NAME = 'landlord_id' 
            AND REFERENCED_TABLE_NAME = 'user_details'
            AND CONSTRAINT_SCHEMA = DATABASE()
        ");

        if (!empty($constraints)) {
            $constraintName = $constraints[0]->CONSTRAINT_NAME;
        }

        // Drop the existing foreign key constraint
        if ($constraintName !== '') {
            Schema::table('property_details', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
        } else {
            // If we couldn't get the name dynamically, try the standard Laravel naming convention
            Schema::table('property_details', function (Blueprint $table) {
                try {
                    $table->dropForeign('property_details_landlord_id_foreign');
                } catch (\Exception $e) {
                    // If this fails, log or output a message
                    echo "Could not drop foreign key with standard name: " . $e->getMessage() . "\n";
                }
            });
        }

        // Add the new foreign key constraint pointing to the users table
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
        // Drop the new foreign key constraint
        Schema::table('property_details', function (Blueprint $table) {
            $table->dropForeign(['landlord_id']);
        });

        // Add back the original foreign key constraint pointing to user_details
        Schema::table('property_details', function (Blueprint $table) {
            $table->foreign('landlord_id')
                  ->references('user_id')
                  ->on('user_details')
                  ->onDelete('cascade');
        });
    }
};
