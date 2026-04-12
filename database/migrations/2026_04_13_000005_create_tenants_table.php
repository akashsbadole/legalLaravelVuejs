<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('logo')->nullable();
            $table->string('primary_color')->default('#3b82f6');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('GSTIN')->nullable();
            $table->string('PAN')->nullable();
            $table->enum('subscription_plan', ['basic', 'professional', 'enterprise'])->default('basic');
            $table->enum('subscription_status', ['active', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->date('subscription_expires_at')->nullable();
            $table->integer('max_users')->default(5);
            $table->integer('max_cases')->default(100);
            $table->integer('max_storage_mb')->default(1024);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
