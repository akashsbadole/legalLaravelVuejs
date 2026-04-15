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
        Schema::create('constitution_articles', function (Blueprint $table) {
            $table->id();
            $table->string('article_number');
            $table->string('part')->nullable(); // Part I, II, III, etc.
            $table->string('title');
            $table->text('content');
            $table->text('explanation')->nullable();
            $table->enum('category', ['Fundamental Rights', 'Directive Principles', 'Fundamental Duties', 'Union and its Territory', 'Citizenship', 'Union Executive', 'Union Legislature', 'Union Judiciary', 'Finance', 'States', 'Local Government', 'Elections', 'Amendment', 'Miscellaneous']);
            $table->json('amendments')->nullable(); // Track constitutional amendments
            $table->json('related_articles')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('article_number');
            $table->index('part');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constitution_articles');
    }
};
