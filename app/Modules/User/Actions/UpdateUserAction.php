<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\MailNotification\Services\MailDispatcherService;
use App\Traits\TransactionTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class UpdateUserAction
{
    use TransactionTrait;

    public function __construct(
        private UserRepository $userRepository,
        private MailDispatcherService $mailDispatcher
    ) {}

    /**
     * Execute the action.
     */
    public function execute(User $user, array $data): User
    {
        return $this->executeInTransaction(function () use ($user, $data) {
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

            $user = $this->userRepository->update($user, $userData);

            // Rolü güncelle
            if (isset($data['role_id'])) {
                if (!empty($data['role_id'])) {
                    $user->roles()->sync([$data['role_id']]);
                } else {
                    $user->roles()->detach();
                }
            }

            // Email değişikliği bildirimi gönder
            if (isset($data['email']) && $data['email'] !== $user->email) {
                $this->sendEmailChangeNotification($user, $data['email']);
            }

            return $user;
        }, 'user update');
    }

    /**
     * Email değişikliği bildirimi gönder
     */
    private function sendEmailChangeNotification(User $user, string $newEmail): void
    {
        $this->mailDispatcher->send([
            'to' => $newEmail,
            'subject' => 'Email Adresiniz Güncellendi - ' . config('app.name'),
            'content' => "Merhaba {$user->full_name},\n\n" .
                        "Hesabınızın email adresi başarıyla güncellendi.\n\n" .
                        "Yeni email adresiniz: {$newEmail}\n\n" .
                        "Bu değişikliği siz yapmadıysanız, lütfen bizimle iletişime geçin.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'email_change',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'old_email' => $user->email,
                'new_email' => $newEmail,
                'action' => 'email_updated'
            ]
        ]);
    }
} 