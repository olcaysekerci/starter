<?php

namespace App\Modules\User\Actions;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\MailNotification\Services\MailDispatcherService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

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
        return $this->transaction(function () use ($data) {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'password' => Hash::make($data['password']),
            ];

            $user = $this->userService->create($userData);

            // Rolleri ata
            if (isset($data['roles']) && is_array($data['roles'])) {
                $user->roles()->sync($data['roles']);
            }

            // İzinleri ata
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->permissions()->sync($data['permissions']);
            }

            return $user;
        });
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