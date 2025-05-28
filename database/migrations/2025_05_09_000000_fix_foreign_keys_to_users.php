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
                tc.table_name,
                kcu.column_name,
                tc.constraint_name
            FROM 
                information_schema.table_constraints tc
            JOIN 
                information_schema.key_column_usage kcu 
                ON tc.constraint_name = kcu.constraint_name
            JOIN 
                information_schema.constraint_column_usage ccu 
                ON ccu.constraint_name = tc.constraint_name
            WHERE 
                tc.constraint_type = 'FOREIGN KEY'
                AND ccu.table_name = 'user_details'
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