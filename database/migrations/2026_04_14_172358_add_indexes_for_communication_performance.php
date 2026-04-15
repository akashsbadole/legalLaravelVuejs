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
            // Indexes for better query performance
            $table->index(['user_id', 'type', 'status'], 'interaction_logs_user_type_status_index');
            $table->index(['created_at'], 'interaction_logs_created_at_index');
            $table->index(['type', 'channel'], 'interaction_logs_type_channel_index');
            $table->index(['sent_at'], 'interaction_logs_sent_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_logs', function (Blueprint $table) {
            $table->dropIndex('interaction_logs_user_type_status_index');
            $table->dropIndex('interaction_logs_created_at_index');
            $table->dropIndex('interaction_logs_type_channel_index');
            $table->dropIndex('interaction_logs_sent_at_index');
        });
    }
};
