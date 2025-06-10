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
        Schema::create('maintenance_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('request_id')->constrained('maintenance_requests', 'request_id')->onDelete('cascade');
            $table->string('sender_type'); // tenant, landlord
            $table->foreignId('sender_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_messages');
    }
}; 