<?php

return [
    'name' => 'MailNotification',
    'enabled' => true,
    'settings' => [
        'mail_notification' => [
            'enabled' => true,
            'log_all_mails' => true,
            'retention_days' => 90,
            'max_retry_attempts' => 3,
            'default_from_email' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'default_from_name' => env('MAIL_FROM_NAME', 'Application'),
        ],
    ],
    'routes' => [
        'web' => true,
        'admin' => true,
    ],
]; 