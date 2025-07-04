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
        Schema::create('property_details', function (Blueprint $table) {
            $table->id('property_id');
            $table->unsignedBigInteger('landlord_id');
            $table->string('property_name');
            $table->string('house_building_number')->nullable(); // ផ្ទះលេខ / អគារលេខ
            $table->string('street')->nullable(); // ផ្លូវ
            $table->string('village')->nullable(); // ភូមិ
            $table->string('commune')->nullable(); // សង្កាត់/ឃុំ
            $table->string('district')->nullable(); // ខណ្ឌ/ស្រុក
            $table->integer('total_floors');
            $table->integer('total_rooms');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
    
            $table->foreign('landlord_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_details');
    }
};
