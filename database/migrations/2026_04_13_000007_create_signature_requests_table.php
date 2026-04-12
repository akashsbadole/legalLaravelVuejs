<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('signer_email');
            $table->string('signer_name');
            $table->string('signer_phone')->nullable();
            $table->enum('status', ['pending', 'sent', 'viewed', 'signed', 'declined', 'expired'])->default('pending');
            $table->timestamp('signed_at')->nullable();
            $table->json('signature_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('signed_document_path')->nullable();
            $table->string('esign_provider')->default('demo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_requests');
    }
};
