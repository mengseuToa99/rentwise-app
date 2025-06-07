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
        Schema::create('lease_agreements', function (Blueprint $table) {
            $table->id('agreement_id');
            $table->foreignId('tenant_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('property_id')->constrained('property_details', 'property_id')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('room_details', 'room_id')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('monthly_rent', 10, 2);
            $table->decimal('security_deposit', 10, 2);
            $table->string('agreement_file_path')->nullable();
            $table->string('status')->default('active'); // active, expired, terminated
            $table->text('terms_conditions');
            $table->boolean('signed_by_tenant')->default(false);
            $table->boolean('signed_by_landlord')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_agreements');
    }
}; 