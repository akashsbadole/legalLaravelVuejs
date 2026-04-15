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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->integer('version_number');
            $table->string('version_label')->nullable(); // e.g., "v1.0", "Draft 1", "Final"
            $table->longText('content'); // Snapshot of content at this version
            $table->json('changes')->nullable(); // Track what changed
            $table->text('change_description')->nullable(); // Description of changes
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->enum('change_type', ['auto_save', 'manual_save', 'ai_generated', 'template_applied', 'user_edit'])->default('manual_save');
            $table->string('file_path')->nullable(); // If version has associated file
            $table->bigInteger('file_size')->nullable();
            $table->boolean('is_current')->default(false); // Mark current active version
            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index(['document_id', 'is_current']);
            $table->index(['created_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
