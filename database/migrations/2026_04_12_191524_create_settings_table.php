<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => 'LegalPro', 'group' => 'general', 'description' => 'Application name'],
            ['key' => 'site_logo', 'value' => null, 'group' => 'general', 'description' => 'Path to site logo'],
            ['key' => 'site_favicon', 'value' => null, 'group' => 'general', 'description' => 'Path to site favicon'],
            ['key' => 'default_language', 'value' => 'en', 'group' => 'general', 'description' => 'Default system language'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'group' => 'general', 'description' => 'Default date format'],
            ['key' => 'timezone', 'value' => 'UTC', 'group' => 'general', 'description' => 'System timezone'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
