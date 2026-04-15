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
        Schema::create('crpc_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('section_number')->unique();
            $table->string('subsection')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('procedure')->nullable();
            $table->enum('court_level', ['Supreme Court', 'High Court', 'Sessions Court', 'Magistrate Court', 'Executive Magistrate']);
            $table->text('timeline_requirements')->nullable();
            $table->text('jurisdiction_details')->nullable();
            $table->json('related_ipc_sections')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_number', 'subsection']);
            $table->index('court_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crpc_sections');
    }
};
