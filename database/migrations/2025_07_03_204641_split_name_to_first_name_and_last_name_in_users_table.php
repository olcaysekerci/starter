<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Önce yeni alanları ekle
            $table->string('first_name')->after('name')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });

        // Mevcut name verilerini first_name ve last_name'e böl
        DB::statement("
            UPDATE users 
            SET 
                first_name = CASE 
                    WHEN LOCATE(' ', name) > 0 THEN LEFT(name, LOCATE(' ', name) - 1)
                    ELSE name 
                END,
                last_name = CASE 
                    WHEN LOCATE(' ', name) > 0 THEN SUBSTRING(name, LOCATE(' ', name) + 1)
                    ELSE '' 
                END
            WHERE name IS NOT NULL AND name != ''
        ");

        // name alanını kaldır
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // name alanını geri ekle
            $table->string('name')->after('id');
        });

        // first_name ve last_name'i birleştirip name'e aktar
        DB::statement("
            UPDATE users 
            SET name = CONCAT(
                COALESCE(first_name, ''),
                CASE 
                    WHEN first_name IS NOT NULL AND last_name IS NOT NULL AND last_name != '' 
                    THEN CONCAT(' ', last_name)
                    ELSE ''
                END
            )
        ");

        // first_name ve last_name alanlarını kaldır
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
