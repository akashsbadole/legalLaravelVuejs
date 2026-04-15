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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'nda', 'employment_contract', 'service_agreement', 'lease_agreement',
                'power_of_attorney', 'legal_notice', 'court_petition', 'lease_deed',
                'sale_deed', 'partnership_deed', 'promissory_note', 'legal_letter',
                'affidavit', 'other',
            ]);
            $table->enum('status', ['draft', 'review', 'approved', 'signed', 'completed', 'archived'])->default('draft');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('document_templates');
            $table->unsignedBigInteger('case_id')->nullable();
            $table->foreign('case_id')->references('id')->on('cases');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users');
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->longText('content'); // Current document content
            $table->json('metadata')->nullable(); // Document-specific metadata
            $table->json('placeholders_filled')->nullable(); // Track which placeholders are filled
            $table->decimal('completion_percentage', 5, 2)->default(0); // Progress tracking
            $table->timestamp('due_date')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_mime_type')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_collaborative')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['created_by', 'is_active']);
            $table->index(['assigned_to', 'due_date']);
            $table->index('case_id');
            $table->index('client_id');
            $table->index('completion_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
