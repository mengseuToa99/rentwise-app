<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Polymorphic images table — replaces the old property_images table.
 * Any model (Property, Unit, MaintenanceRequest, etc.) can attach images
 * via morphMany(Image::class, 'imageable').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id('image_id');
            $table->morphs('imageable'); // imageable_id + imageable_type, indexed
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('uploaded_by_user_id')->nullable()->references('user_id')->on('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['imageable_type', 'imageable_id', 'is_primary'], 'idx_images_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
