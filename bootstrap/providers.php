<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    App\Modules\User\UserServiceProvider::class,
    App\Modules\Dashboard\DashboardServiceProvider::class,
    App\Modules\Settings\SettingsServiceProvider::class,
    App\Modules\ActivityLog\ActivityLogServiceProvider::class,
    App\Modules\MailNotification\MailNotificationServiceProvider::class,
];
