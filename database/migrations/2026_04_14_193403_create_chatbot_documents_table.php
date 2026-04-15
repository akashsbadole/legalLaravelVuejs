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
        Schema::create('chatbot_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('document_type', [
                'training_data', 'conversation_log', 'knowledge_base', 'configuration',
                'api_documentation', 'user_guide', 'system_manual', 'analytics_report',
                'error_log', 'performance_metrics', 'conversation_flow', 'decision_tree',
                'fallback_response', 'custom_script', 'integration_guide', 'other',
            ]);
            $table->enum('file_format', [
                'pdf', 'doc', 'docx', 'txt', 'md', 'json', 'xml', 'csv',
                'jpg', 'png', 'gif', 'svg', 'mp4', 'avi', 'zip', 'rar',
            ]);
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_url')->nullable();
            $table->bigInteger('file_size'); // in bytes
            $table->string('mime_type');
            $table->json('extracted_text')->nullable(); // OCR/text extraction results
            $table->json('ai_analysis')->nullable(); // AI processing results
            $table->json('metadata')->nullable(); // Additional file metadata
            $table->string('language', 10)->default('en'); // Document language
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('chatbot_document_categories');
            $table->unsignedBigInteger('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->unsignedBigInteger('last_modified_by')->nullable();
            $table->foreign('last_modified_by')->references('id')->on('users');
            $table->enum('processing_status', [
                'pending', 'processing', 'completed', 'failed',
            ])->default('pending');
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->boolean('is_indexed')->default(false); // For search indexing
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_public')->default(false); // Public access
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['document_type', 'is_active']);
            $table->index(['file_format', 'processing_status']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index(['is_indexed', 'processing_status']);
            $table->index('category_id');
            $table->index('last_accessed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_documents');
    }
};
