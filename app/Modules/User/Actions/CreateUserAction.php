<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\MailNotification\Services\MailDispatcherService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CreateUserAction
{
    public function __construct(
        private UserRepository $userRepository,
        private MailDispatcherService $mailDispatcher
    ) {}

    /**
     * Execute the action.
     */
    public function execute(array $data): User
    {
        // Şifreyi hash'le
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Rolleri ayır
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        // Varsayılan değerleri ayarla
        $data['is_active'] = $data['is_active'] ?? true;

        // Kullanıcıyı oluştur
        $user = $this->userRepository->create($data);

        // Rolleri ata
        if (!empty($roles)) {
            $user->assignRole($roles);
        }

        // Hoş geldin maili gönder
        $this->sendWelcomeEmail($user);

        Log::info('Yeni kullanıcı oluşturuldu', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $roles
        ]);

        return $user;
    }

    /**
     * Hoş geldin maili gönder
     */
    private function sendWelcomeEmail(User $user): void
    {
        $this->mailDispatcher->send([
            'to' => $user->email,
            'subject' => 'Hoş Geldiniz - ' . config('app.name'),
            'content' => "Merhaba {$user->name},\n\n" .
                        "Sitemize hoş geldiniz! Hesabınız başarıyla oluşturuldu.\n\n" .
                        "Hesabınızla ilgili herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'welcome',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => 'user_created'
            ]
        ]);
    }
} 