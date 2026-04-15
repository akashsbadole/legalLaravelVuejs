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
        Schema::create('cpc_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('section_number')->unique();
            $table->string('subsection')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('procedure')->nullable();
            $table->enum('court_level', ['Supreme Court', 'High Court', 'District Court', 'Civil Court', 'Small Causes Court']);
            $table->text('timeline_requirements')->nullable();
            $table->decimal('monetary_jurisdiction', 15, 2)->nullable(); // In rupees
            $table->text('appeal_provisions')->nullable();
            $table->json('related_sections')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_number', 'subsection']);
            $table->index('court_level');
            $table->index('monetary_jurisdiction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpc_sections');
    }
};
