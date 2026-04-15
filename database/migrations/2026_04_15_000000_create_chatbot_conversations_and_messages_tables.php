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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('session_id')->unique();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->integer('message_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->enum('sender', ['user', 'assistant']);
            $table->text('message');
            $table->json('references')->nullable();
            $table->boolean('is_bookmarked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chatbot_conversations');
    }
};
