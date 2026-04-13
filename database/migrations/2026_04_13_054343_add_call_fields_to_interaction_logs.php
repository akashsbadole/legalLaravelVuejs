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
            $table->foreignId('case_id')->nullable()->constrained()->nullOnDelete()->after('user_id');
            $table->integer('call_duration')->nullable()->after('body'); // in seconds
            $table->string('call_outcome')->nullable()->after('call_duration'); // answered, voicemail, no_answer, busy
            $table->string('phone_number')->nullable()->after('recipients');
            $table->text('call_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_logs', function (Blueprint $table) {
            $table->dropForeign(['case_id']);
            $table->dropColumn(['case_id', 'call_duration', 'call_outcome', 'phone_number', 'call_notes']);
        });
    }
};
