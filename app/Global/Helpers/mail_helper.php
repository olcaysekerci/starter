<?php

use App\Modules\MailNotification\Services\MailDispatcherService;

if (!function_exists('send_mail')) {
    /**
     * Mail gönder ve logla
     */
    function send_mail(array $data): bool
    {
        $mailDispatcher = app(MailDispatcherService::class);
        return $mailDispatcher->send($data);
    }
}

if (!function_exists('send_test_mail')) {
    /**
     * Test mail gönder
     */
    function send_test_mail(string $to, string $subject = 'Test Mail'): bool
    {
        $mailDispatcher = app(MailDispatcherService::class);
        return $mailDispatcher->sendTestMail($to, $subject);
    }
} 