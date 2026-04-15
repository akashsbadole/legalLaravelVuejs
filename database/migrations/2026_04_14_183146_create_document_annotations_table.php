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
        Schema::create('document_annotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('annotation_type', ['highlight', 'underline', 'strikethrough', 'note', 'link', 'image', 'signature']);
            $table->integer('start_position');
            $table->integer('end_position');
            $table->string('selected_text')->nullable();
            $table->text('note')->nullable(); // For note-type annotations
            $table->string('color')->default('#FFFF00'); // Highlight color
            $table->json('position_data')->nullable(); // For PDF/image annotations (x, y, width, height)
            $table->string('link_url')->nullable(); // For link annotations
            $table->string('image_path')->nullable(); // For image annotations
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['document_id', 'annotation_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['start_position', 'end_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_annotations');
    }
};
