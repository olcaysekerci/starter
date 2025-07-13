<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\MailNotification\Services\MailNotificationService;
use App\Traits\TransactionTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CreateUserAction
{
    use TransactionTrait;

    public function __construct(
        private UserRepository $userRepository,
        private MailNotificationService $mailDispatcher
    ) {}

    /**
     * Execute the action.
     */
    public function execute(array $data): User
    {
        return $this->executeInTransaction(function () use ($data) {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'password' => Hash::make($data['password']),
            ];

            $user = $this->userRepository->create($userData);

            // Rolü ata
            if (isset($data['role_id']) && !empty($data['role_id'])) {
                $user->roles()->sync([$data['role_id']]);
            }

            // Hoş geldin maili gönder
            $this->sendWelcomeEmail($user);

            return $user;
        }, 'user creation');
    }

    /**
     * Hoş geldin maili gönder
     */
    private function sendWelcomeEmail(User $user): void
    {
        $this->mailDispatcher->send([
            'to' => $user->email,
            'subject' => 'Hoş Geldiniz - ' . config('app.name'),
            'content' => "Merhaba {$user->full_name},\n\n" .
                        "Sitemize hoş geldiniz! Hesabınız başarıyla oluşturuldu.\n\n" .
                        "Hesabınızla ilgili herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'welcome',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'action' => 'user_created'
            ]
        ]);
    }
} 