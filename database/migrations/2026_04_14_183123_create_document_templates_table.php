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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('category', [
                'nda', 'employment_contract', 'service_agreement', 'lease_agreement',
                'power_of_attorney', 'legal_notice', 'court_petition', 'lease_deed',
                'sale_deed', 'partnership_deed', 'promissory_note', 'legal_letter',
                'affidavit', 'other',
            ]);
            $table->text('description');
            $table->longText('content'); // Template content with placeholders
            $table->json('placeholders'); // Available placeholders for template filling
            $table->json('metadata')->nullable(); // Template-specific metadata
            $table->string('icon')->nullable(); // Icon for UI display
            $table->string('color')->default('#3B82F6'); // Theme color for UI
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
