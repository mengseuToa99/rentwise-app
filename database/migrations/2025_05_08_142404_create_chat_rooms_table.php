<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->enum('type', ['private', 'group', 'support'])->default('private');
            $table->foreignId('created_by')->constrained('users', 'user_id');

            // Polymorphic context: link a room to a Rental, MaintenanceRequest, etc.
            $table->nullableMorphs('related');

            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at', 'idx_chat_rooms_created');
            $table->index('updated_at', 'idx_chat_rooms_updated');
        });

        Schema::create('chat_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_muted')->default(false);
            $table->timestamps();

            $table->unique(['chat_room_id', 'user_id']);
            $table->index(['chat_room_id', 'last_read_at'], 'idx_chat_participants_room_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_participants');
        Schema::dropIfExists('chat_rooms');
    }
};
