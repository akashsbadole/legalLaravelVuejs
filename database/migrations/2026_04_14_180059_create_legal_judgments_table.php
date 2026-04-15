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
        Schema::create('legal_judgments', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('case_title');
            $table->enum('court', ['Supreme Court', 'High Court', 'District Court', 'Tribunal']);
            $table->string('state')->nullable(); // For High Courts
            $table->string('bench')->nullable(); // Constitution Bench, Division Bench, etc.
            $table->json('judges'); // Array of judge names
            $table->date('judgment_date');
            $table->text('facts')->nullable();
            $table->text('issues')->nullable();
            $table->text('arguments_petitioner')->nullable();
            $table->text('arguments_respondent')->nullable();
            $table->text('judgment_summary');
            $table->text('full_judgment')->nullable(); // Store full text if available
            $table->string('citation')->nullable(); // AIR, SCC, etc.
            $table->enum('outcome', ['allowed', 'dismissed', 'partially allowed', 'referred', 'other']);
            $table->json('legal_principles')->nullable(); // Key legal principles established
            $table->json('statutes_referred')->nullable(); // IPC/CrPC/CPC sections cited
            $table->json('precedent_cases')->nullable(); // Cases relied upon
            $table->text('dissenting_opinion')->nullable();
            $table->string('reporter')->nullable(); // AIR, SCC, etc.
            $table->integer('reporter_volume')->nullable();
            $table->integer('reporter_page')->nullable();
            $table->boolean('is_landmark')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['court', 'state']);
            $table->index('judgment_date');
            $table->index('outcome');
            $table->index('is_landmark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_judgments');
    }
};
