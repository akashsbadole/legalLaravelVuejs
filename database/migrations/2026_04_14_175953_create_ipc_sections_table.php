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
        Schema::create('ipc_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('section_number')->unique();
            $table->string('subsection')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('penalty')->nullable();
            $table->enum('classification', ['bailable', 'non-bailable', 'compoundable', 'non-compoundable']);
            $table->enum('cognizable', ['cognizable', 'non-cognizable']);
            $table->text('punishment_details')->nullable();
            $table->json('related_sections')->nullable(); // For cross-references
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_number', 'subsection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipc_sections');
    }
};
