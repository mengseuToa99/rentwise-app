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
        Schema::create('maintenance_photos', function (Blueprint $table) {
            $table->id('photo_id');
            $table->foreignId('request_id')->constrained('maintenance_requests', 'request_id')->onDelete('cascade');
            $table->string('photo_path');
            $table->string('photo_type')->default('before'); // before, after
            $table->string('uploaded_by_type'); // tenant, landlord
            $table->foreignId('uploaded_by_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->text('caption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_photos');
    }
}; 