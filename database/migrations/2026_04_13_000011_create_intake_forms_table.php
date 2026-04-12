<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intake_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('set null');
            $table->string('name');
            $table->json('fields_json');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->text('success_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->json('notify_emails')->nullable();
            $table->boolean('create_lead')->default(true);
            $table->foreignId('assign_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('intake_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('intake_forms')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->json('data_json');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['new', 'reviewed', 'contacted', 'converted', 'rejected'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intake_submissions');
        Schema::dropIfExists('intake_forms');
    }
};
