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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('alternate_phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country')->default('India');

            // Indian-specific identification fields
            $table->string('aadhaar_number')->unique()->nullable();
            $table->string('pan_number')->unique()->nullable();
            $table->string('gstin')->unique()->nullable();

            // Client type and classification
            $table->enum('client_type', ['individual', 'corporate', 'government', 'ngo'])->default('individual');
            $table->string('company_name')->nullable();
            $table->string('designation')->nullable();

            // KYC/AML Compliance fields
            $table->enum('kyc_status', ['pending', 'verified', 'rejected', 'expired'])->default('pending');
            $table->timestamp('kyc_verified_at')->nullable();
            $table->foreignId('kyc_verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Client segmentation and relationship management
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('client_segment')->nullable(); // e.g., 'high-value', 'corporate', 'individual'
            $table->text('notes')->nullable();

            // Communication preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('whatsapp_notifications')->default(false);

            // Consent management for PDPB compliance
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('data_processing_consent')->default(false);
            $table->timestamp('consent_updated_at')->nullable();

            // Status and timestamps
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['first_name', 'last_name']);
            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['aadhaar_number']);
            $table->index(['pan_number']);
            $table->index(['gstin']);
            $table->index(['client_type']);
            $table->index(['kyc_status']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
