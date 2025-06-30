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
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('permissions', 'description')) {
                $table->text('description')->nullable()->after('display_name');
            }
            if (!Schema::hasColumn('permissions', 'module')) {
                $table->string('module')->nullable()->after('description');
            }
            if (!Schema::hasColumn('permissions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('module');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'description', 'module', 'is_active']);
        });
    }
};
