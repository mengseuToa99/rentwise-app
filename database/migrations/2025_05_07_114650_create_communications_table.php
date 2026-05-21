<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('sender_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->references('user_id')->on('users')->onDelete('cascade');

            $table->string('subject')->nullable();
            $table->text('message');

            // Polymorphic context: link a message to a Rental, MaintenanceRequest, Invoice, etc.
            $table->nullableMorphs('subject_context'); // subject_context_id + subject_context_type

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'receiver_id'], 'idx_comm_sender_receiver');
            $table->index(['receiver_id', 'is_read'], 'idx_comm_receiver_unread');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
