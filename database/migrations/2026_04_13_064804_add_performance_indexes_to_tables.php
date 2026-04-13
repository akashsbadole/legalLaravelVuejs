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
        // Cases table indexes
        Schema::table('cases', function (Blueprint $table) {
            $table->index(['status_id', 'assigned_attorney_id']);
            $table->index(['client_id', 'created_at']);
            $table->index('case_type');
        });

        // Enquiries table indexes
        Schema::table('enquiries', function (Blueprint $table) {
            $table->index(['status', 'priority', 'created_at']);
            $table->index('assigned_to');
            $table->index(['category', 'status']);
        });

        // Time entries indexes
        Schema::table('time_entries', function (Blueprint $table) {
            $table->index(['case_id', 'status', 'work_date']);
            $table->index(['user_id', 'work_date']);
        });

        // Invoices indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['client_id', 'status', 'due_date']);
            $table->index(['issue_date', 'due_date']);
        });

        // Interaction logs indexes
        Schema::table('interaction_logs', function (Blueprint $table) {
            $table->index(['user_id', 'type', 'sent_at']);
            $table->index(['case_id', 'type', 'sent_at']);
        });

        // Audit logs indexes
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['entity_type', 'entity_id']);
        });

        Schema::table('interaction_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type', 'sent_at']);
            $table->dropIndex(['case_id', 'type', 'sent_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'status', 'due_date']);
            $table->dropIndex(['issue_date', 'due_date']);
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropIndex(['case_id', 'status', 'work_date']);
            $table->dropIndex(['user_id', 'work_date']);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority', 'created_at']);
            $table->dropIndex(['assigned_to']);
            $table->dropIndex(['category', 'status']);
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropIndex(['status_id', 'assigned_attorney_id']);
            $table->dropIndex(['client_id', 'created_at']);
            $table->dropIndex(['case_type']);
        });
    }
};
