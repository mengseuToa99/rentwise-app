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
        Schema::create('room_details', function (Blueprint $table) {
            $table->id('room_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('pricing_group_id')->nullable();
            $table->string('room_name')->nullable();
            $table->integer('floor_number');
            $table->string('room_number');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('available')->default(true);
            $table->string('status')->default('vacant');
            $table->decimal('rent_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->timestamps();
    
            $table->foreign('property_id')->references('property_id')->on('property_details')->onDelete('cascade');
            $table->foreign('pricing_group_id')->references('group_id')->on('pricing_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_details');
    }
};
