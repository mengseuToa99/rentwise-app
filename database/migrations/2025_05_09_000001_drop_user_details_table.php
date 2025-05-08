<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if user_details table exists before dropping
        if (Schema::hasTable('user_details')) {
            Schema::dropIfExists('user_details');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We can't easily recreate the table since we've migrated its data,
        // so this is a one-way migration
    }
} 