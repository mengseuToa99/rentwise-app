<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id('log_id');

            // Nullable so system / cron / unauthenticated actions can be logged
            $table->foreignId('user_id')->nullable()->references('user_id')->on('users')->nullOnDelete();

            $table->string('action'); // created, updated, deleted, login, etc.
            $table->text('description');

            // Polymorphic — what was acted on (User, Property, Rental, ...)
            $table->nullableMorphs('subject'); // subject_id + subject_type

            // Audit metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('changes')->nullable(); // diff payload for updates

            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'idx_logs_user_time');
            $table->index('action', 'idx_logs_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
