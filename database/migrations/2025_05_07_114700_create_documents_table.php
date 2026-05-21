<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('document_id');

            // Polymorphic owner — attach docs to User, Rental, MaintenanceRequest, Invoice, Property, etc.
            $table->morphs('documentable'); // documentable_id + documentable_type, indexed

            $table->foreignId('uploaded_by_user_id')->nullable()->references('user_id')->on('users')->nullOnDelete();

            $table->string('document_type'); // free-form: lease_agreement, payment_receipt, id_card, contract, photo, etc.
            $table->string('original_filename')->nullable();
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id', 'document_type'], 'idx_documents_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
