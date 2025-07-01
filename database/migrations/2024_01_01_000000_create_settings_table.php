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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->index(); // 'app', 'mail', 'security'
            $table->string('key', 100)->index(); // 'site_name', 'smtp_host', etc.
            $table->text('value')->nullable(); // Ayar değeri
            $table->string('type', 20)->default('string'); // 'string', 'boolean', 'integer', 'json'
            $table->text('description')->nullable(); // Ayar açıklaması
            $table->boolean('is_public')->default(false); // Frontend'de görünür mü?
            $table->timestamps();
            
            $table->unique(['category', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
}; 