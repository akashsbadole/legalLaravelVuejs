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
        Schema::table('cases', function (Blueprint $table) {
            $table->json('assigned_staff')->nullable()->after('assigned_attorney_id'); // Track all assigned staff
            $table->timestamp('assignment_changed_at')->nullable()->after('assigned_staff');
            $table->foreignId('assignment_changed_by')->nullable()->constrained('users')->nullOnDelete()->after('assignment_changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropForeign(['assignment_changed_by']);
            $table->dropColumn(['assigned_staff', 'assignment_changed_at', 'assignment_changed_by']);
        });
    }
};
