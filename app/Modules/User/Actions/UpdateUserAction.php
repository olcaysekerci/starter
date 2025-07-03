<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\MailNotification\Services\MailDispatcherService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class UpdateUserAction
{
    public function __construct(
        private UserRepository $userRepository,
        private MailDispatcherService $mailDispatcher
    ) {}

    /**
     * Execute the action.
     */
    public function execute(User $user, array $data): User
    {
        return $this->transaction(function () use ($user, $data) {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ];

            // Şifre güncelleniyorsa hash'le
            if (isset($data['password']) && !empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user = $this->userService->update($user, $userData);

            // Rolleri güncelle
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            // İzinleri güncelle
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->permissions()->sync($data['permissions']);
            }

            return $user;
        });
    }

    /**
     * Email değişikliği bildirimi gönder
     */
    private function sendEmailChangeNotification(User $user, string $newEmail): void
    {
        $this->mailDispatcher->send([
            'to' => $newEmail,
            'subject' => 'Email Adresiniz Güncellendi - ' . config('app.name'),
            'content' => "Merhaba {$user->name},\n\n" .
                        "Hesabınızın email adresi başarıyla güncellendi.\n\n" .
                        "Yeni email adresiniz: {$newEmail}\n\n" .
                        "Bu değişikliği siz yapmadıysanız, lütfen bizimle iletişime geçin.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'email_change',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'old_email' => $user->email,
                'new_email' => $newEmail,
                'action' => 'email_updated'
            ]
        ]);
    }
} 