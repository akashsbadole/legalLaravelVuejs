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
        Schema::create('legal_references', function (Blueprint $table) {
            $table->id();
            $table->enum('source_type', ['ipc', 'crpc', 'cpc', 'constitution', 'state_law', 'judgment']);
            $table->unsignedBigInteger('source_id'); // Foreign key to the source table
            $table->enum('reference_type', ['ipc', 'crpc', 'cpc', 'constitution', 'state_law', 'judgment', 'external']);
            $table->string('reference_citation'); // The citation text
            $table->text('reference_context')->nullable(); // Context where it's referenced
            $table->string('url')->nullable(); // Link to external source
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index(['reference_type', 'reference_citation']);
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_references');
    }
};
