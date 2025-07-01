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
    public function execute(int $id, array $data): bool
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new ModelNotFoundException('Kullanıcı bulunamadı.');
        }

        // Email değişikliği kontrolü
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $id) {
                throw new \InvalidArgumentException('Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.');
            }
        }

        // Şifre değişikliği kontrolü
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Rolleri ayır
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        // Kullanıcıyı güncelle
        $updated = $this->userRepository->update($id, $data);

        if (!$updated) {
            throw new \Exception('Kullanıcı güncellenirken bir hata oluştu.');
        }

        // Rolleri güncelle
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        // Email değişikliği varsa bilgilendirme maili gönder
        if (isset($data['email']) && $data['email'] !== $user->email) {
            $this->sendEmailChangeNotification($user, $data['email']);
        }

        Log::info('Kullanıcı güncellendi', [
            'user_id' => $id,
            'updated_fields' => Arr::keys($data),
            'roles' => $roles
        ]);

        return true;
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