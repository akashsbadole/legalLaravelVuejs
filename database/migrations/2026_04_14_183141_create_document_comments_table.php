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
        Schema::create('document_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('content');
            $table->integer('start_position')->nullable(); // For text highlighting
            $table->integer('end_position')->nullable();
            $table->string('highlighted_text')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // For threaded comments
            $table->foreign('parent_id')->references('id')->on('document_comments')->onDelete('cascade');
            $table->enum('comment_type', ['general', 'suggestion', 'question', 'issue', 'approval'])->default('general');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['document_id', 'is_active']);
            $table->index(['parent_id', 'created_at']);
            $table->index(['is_resolved', 'resolved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_comments');
    }
};
