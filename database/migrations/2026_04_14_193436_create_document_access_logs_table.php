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
        Schema::create('chatbot_document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('chatbot_documents')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('action', [
                'view', 'download', 'upload', 'edit', 'delete', 'share',
                'search', 'export', 'print', 'annotate', 'version_create',
            ]);
            $table->enum('access_type', ['web', 'api', 'mobile', 'integration'])->default('web');
            $table->json('additional_data')->nullable(); // Action-specific data
            $table->boolean('was_successful')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('accessed_at');
            $table->timestamps();

            $table->index(['document_id', 'accessed_at']);
            $table->index(['user_id', 'action', 'accessed_at']);
            $table->index(['action', 'was_successful']);
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_document_access_logs');
    }
};
