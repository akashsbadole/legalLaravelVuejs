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
        Schema::table('interaction_logs', function (Blueprint $table) {
            // Add whatsapp to the type enum
            $table->enum('type', ['call', 'email', 'sms', 'meeting', 'message', 'whatsapp'])->change();

            // Add channel field to distinguish between SMS and WhatsApp when type is sms/whatsapp
            $table->string('channel')->default('sms')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_logs', function (Blueprint $table) {
            $table->enum('type', ['call', 'email', 'sms', 'meeting', 'message'])->change();
            $table->dropColumn('channel');
        });
    }
};
