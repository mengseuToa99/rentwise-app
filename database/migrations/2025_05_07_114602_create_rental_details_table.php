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
        Schema::create('rental_details', function (Blueprint $table) {
            $table->id('rental_id');
            $table->unsignedBigInteger('landlord_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('room_id');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->string('lease_agreement')->nullable();
            $table->timestamps();
    
            $table->foreign('landlord_id')->references('user_id')->on('user_details')->onDelete('cascade');
            $table->foreign('tenant_id')->references('user_id')->on('user_details')->onDelete('cascade');
            $table->foreign('room_id')->references('room_id')->on('room_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_details');
    }
};
