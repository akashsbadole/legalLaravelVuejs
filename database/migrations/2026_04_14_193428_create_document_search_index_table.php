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
        Schema::create('chatbot_document_search_index', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('chatbot_documents')->onDelete('cascade');
            $table->longText('content'); // Full searchable content
            $table->json('keywords')->nullable(); // Extracted keywords
            $table->json('entities')->nullable(); // Named entities (people, organizations, etc.)
            $table->decimal('relevance_score', 3, 2)->nullable(); // AI-calculated relevance
            $table->json('search_vectors')->nullable(); // For semantic search
            $table->timestamp('indexed_at');
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->unique(['document_id', 'is_current']);
            $table->index(['indexed_at', 'is_current']);
            // Note: In production, consider using Elasticsearch or similar for better full-text search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_document_search_index');
    }
};
