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
        Schema::table('users', function (Blueprint $table) {
            // Add social login fields
            $table->string('google_id')->nullable()->after('status');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('telegram_id')->nullable()->after('facebook_id');
            $table->string('phone')->nullable()->after('telegram_id');
            $table->string('avatar')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove social login fields
            $table->dropColumn([
                'google_id',
                'facebook_id',
                'telegram_id',
                'phone',
                'avatar'
            ]);
        });
    }
};
