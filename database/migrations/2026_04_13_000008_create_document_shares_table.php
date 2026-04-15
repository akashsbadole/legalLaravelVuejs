<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('shared_by')->constrained('users')->onDelete('cascade');
            $table->string('shared_with_email');
            $table->string('shared_with_name')->nullable();
            $table->enum('access_level', ['view', 'download', 'edit'])->default('view');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('password_protected')->default(false);
            $table->string('password')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->integer('max_views')->nullable();
            $table->string('share_token', 64)->unique();
            $table->timestamps();

            $table->index('share_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_shares');
    }
};
