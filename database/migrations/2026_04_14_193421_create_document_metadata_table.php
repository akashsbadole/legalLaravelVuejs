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
        Schema::create('chatbot_document_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('chatbot_documents')->onDelete('cascade');
            $table->string('key'); // Metadata key (e.g., 'author', 'version', 'tags')
            $table->text('value'); // Metadata value
            $table->enum('value_type', ['string', 'number', 'boolean', 'date', 'array', 'object'])->default('string');
            $table->boolean('is_system')->default(false); // System-generated metadata
            $table->boolean('is_searchable')->default(true); // Include in search
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();

            $table->unique(['document_id', 'key']);
            $table->index(['key', 'value_type']);
            $table->index(['is_searchable', 'is_system']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_document_metadata');
    }
};
