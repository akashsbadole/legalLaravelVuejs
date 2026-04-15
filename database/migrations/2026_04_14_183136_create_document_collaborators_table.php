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
        Schema::create('document_collaborators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('permission_level', ['viewer', 'editor', 'admin'])->default('viewer');
            $table->enum('access_type', ['read', 'write', 'comment', 'approve'])->default('read');
            $table->timestamp('expires_at')->nullable(); // For temporary access
            $table->boolean('can_download')->default(true);
            $table->boolean('can_share')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->unsignedBigInteger('invited_by');
            $table->foreign('invited_by')->references('id')->on('users');
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['document_id', 'user_id']);
            $table->index(['user_id', 'is_active']);
            $table->index(['expires_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_collaborators');
    }
};
