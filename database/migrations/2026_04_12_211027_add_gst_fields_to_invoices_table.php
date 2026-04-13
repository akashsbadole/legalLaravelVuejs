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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('gstin')->nullable()->after('client_id');
            $table->string('sac_code')->nullable()->after('gstin');
            $table->decimal('cgst_amount', 10, 2)->nullable()->after('tax_amount');
            $table->decimal('sgst_amount', 10, 2)->nullable()->after('cgst_amount');
            $table->decimal('igst_amount', 10, 2)->nullable()->after('sgst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'sac_code', 'cgst_amount', 'sgst_amount', 'igst_amount']);
        });
    }
};
