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
        Schema::create('state_laws', function (Blueprint $table) {
            $table->id();
            $table->enum('state', [
                'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
                'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
                'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
                'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
                'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
                'Delhi', 'Puducherry', 'Lakshadweep', 'Daman and Diu', 'Dadra and Nagar Haveli',
            ]);
            $table->string('law_name');
            $table->string('law_type'); // Act, Rules, Regulations, etc.
            $table->integer('section_number')->nullable();
            $table->string('subsection')->nullable();
            $table->text('content');
            $table->text('description')->nullable();
            $table->date('enactment_date')->nullable();
            $table->json('amendments')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['state', 'law_name']);
            $table->index(['section_number', 'subsection']);
            $table->index('law_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_laws');
    }
};
