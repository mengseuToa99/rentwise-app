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
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('type')->default('private'); // private or group
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create pivot table for chat room participants
        Schema::create('chat_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['chat_room_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_participants');
        Schema::dropIfExists('chat_rooms');
    }
};
