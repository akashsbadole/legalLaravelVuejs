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
        Schema::create('chatbot_document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon for UI display
            $table->string('color', 7)->default('#3B82F6'); // Hex color for UI
            $table->enum('category_type', [
                'training', 'configuration', 'documentation', 'analytics',
                'logs', 'flows', 'scripts', 'guides', 'other',
            ]);
            $table->json('allowed_file_types')->nullable(); // Array of allowed file extensions
            $table->integer('max_file_size')->default(10485760); // 10MB default
            $table->boolean('auto_process')->default(true); // Auto AI processing
            $table->boolean('requires_approval')->default(false); // Manual approval needed
            $table->integer('document_count')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('chatbot_document_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_type', 'is_active']);
            $table->index(['parent_id', 'sort_order']);
            $table->unique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_document_categories');
    }
};
