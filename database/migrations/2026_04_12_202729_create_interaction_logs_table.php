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
        Schema::create('interaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // client or user
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['call', 'email', 'sms', 'meeting', 'message']);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->json('recipients')->nullable(); // for emails/sms
            $table->timestamp('sent_at')->nullable();
            $table->string('direction')->default('outbound'); // inbound/outbound
            $table->string('status')->default('sent'); // sent, delivered, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interaction_logs');
    }
};
