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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', [
                'active',
                'inactive', 
                'pending_verification',
                'suspended',
                'banned',
                'deleted'
            ])->default('active')->after('email');
            
            $table->enum('type', [
                'admin',
                'moderator',
                'web'
            ])->default('web')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'type']);
        });
    }
}; 