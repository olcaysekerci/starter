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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient'); // Alıcı email
            $table->string('subject'); // Mail konusu
            $table->longText('content'); // Mail içeriği
            $table->string('type')->default('notification'); // Mail türü (welcome, reset, test, etc.)
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable(); // Gönderim tarihi
            $table->text('error_message')->nullable(); // Hata mesajı
            $table->string('ip_address')->nullable(); // IP adresi
            $table->text('user_agent')->nullable(); // User agent
            $table->json('metadata')->nullable(); // Ek veriler
            $table->integer('retry_count')->default(0); // Yeniden deneme sayısı
            $table->timestamp('last_retry_at')->nullable(); // Son deneme tarihi
            $table->timestamps();
            $table->softDeletes();

            // İndeksler
            $table->index(['recipient']);
            $table->index(['status']);
            $table->index(['type']);
            $table->index(['created_at']);
            $table->index(['sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
