<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trust_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('case_id')->nullable()->constrained('cases')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'adjustment', 'transfer_to_operating']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->date('transaction_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('trust_account_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trust_account_id')->constrained('trust_accounts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('action');
            $table->decimal('amount_before', 12, 2);
            $table->decimal('amount_after', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trust_account_activities');
        Schema::dropIfExists('trust_accounts');
    }
};
