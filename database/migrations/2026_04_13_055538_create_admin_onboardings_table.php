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
        Schema::create('admin_onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->json('completed_steps')->nullable();
            $table->integer('current_step')->default(1);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_onboardings');
    }
};
