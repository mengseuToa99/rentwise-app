<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixForeignKeysToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the list of tables with foreign keys to user_details
        $foreignKeys = $this->getForeignKeysToUserDetails();
        
        foreach ($foreignKeys as $foreignKey) {
            $tableName = $foreignKey->TABLE_NAME;
            $constraintName = $foreignKey->CONSTRAINT_NAME;
            $columnName = $foreignKey->COLUMN_NAME;
            
            // Drop the existing foreign key
            Schema::table($tableName, function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
            
            // Add new foreign key to users table
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->foreign($columnName)
                      ->references('user_id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a cleanup migration, no down action needed
        // as we can't revert to pointing to a non-existent table
    }
    
    /**
     * Get foreign keys pointing to user_details table
     */
    private function getForeignKeysToUserDetails()
    {
        return DB::select("
            SELECT
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME
            FROM 
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE
                REFERENCED_TABLE_NAME = 'user_details'
                AND TABLE_SCHEMA = DATABASE()
        ");
    }
} 