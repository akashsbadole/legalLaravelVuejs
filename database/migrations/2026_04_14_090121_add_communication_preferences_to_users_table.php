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
        Schema::table('users', function (Blueprint $table) {
            $table->json('communication_preferences')->nullable()->after('phone'); // e.g., ['email', 'sms', 'whatsapp']
            $table->boolean('communication_opt_out')->default(false)->after('communication_preferences');
            $table->timestamp('gdpr_consent_given_at')->nullable()->after('communication_opt_out');
            $table->string('gdpr_consent_ip')->nullable()->after('gdpr_consent_given_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['communication_preferences', 'communication_opt_out', 'gdpr_consent_given_at', 'gdpr_consent_ip']);
        });
    }
};
