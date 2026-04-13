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
        Schema::create('case_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type'); // deadline, hearing, meeting, review, client_followup
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('reminder_at')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'sent', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['case_id', 'scheduled_at']);
            $table->index(['assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_follow_ups');
    }
};
