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
        $this->updateForeignKeys();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Since we're consolidating user tables, there's no need for a rollback
        // as the user_details table will no longer exist
    }
    
    /**
     * Get foreign keys pointing to user_details table
     */
    private function getForeignKeysToUserDetails()
    {
        return DB::select("
            SELECT DISTINCT
                kcu.table_name,
                kcu.column_name,
                kcu.constraint_name
            FROM 
                information_schema.key_column_usage kcu 
            WHERE 
                kcu.table_schema = DATABASE()
                AND kcu.referenced_table_name = 'user_details'
                AND kcu.referenced_column_name IS NOT NULL
        ");
    }

    private function updateForeignKeys()
    {
        $foreignKeys = $this->getForeignKeysToUserDetails();

        foreach ($foreignKeys as $fk) {
            Schema::table($fk->table_name, function (Blueprint $table) use ($fk) {
                // Drop the existing foreign key
                $table->dropForeign($fk->constraint_name);

                // Add the new foreign key pointing to users table
                $table->foreign($fk->column_name)
                      ->references('user_id')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }
} 
