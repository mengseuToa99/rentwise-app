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
        Schema::create('pricing_groups', function (Blueprint $table) {
            $table->id('group_id');
            $table->unsignedBigInteger('property_id');
            $table->string('group_name');
            $table->string('room_type');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->json('amenities')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('property_id')->references('property_id')->on('property_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_groups');
    }
};
