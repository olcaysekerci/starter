<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\MailNotification\Services\MailDispatcherService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private MailDispatcherService $mailDispatcher
    ) {}

    /**
     * Tüm kullanıcıları getir (sayfalama olmadan)
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Tüm kullanıcıları sayfalı olarak getir
     */
    public function getAllUsersWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($perPage);
    }

    /**
     * Kullanıcı detayını getir
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Kullanıcı detayını DTO olarak getir
     */
    public function getUserDTOById(int $id): ?UserDTO
    {
        $user = $this->userRepository->findById($id);
        
        return $user ? UserDTO::fromArray($user->toArray()) : null;
    }

    /**
     * Yeni kullanıcı oluştur
     */
    public function createUser(array $data): User
    {
        // Rolleri ayır
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $user = $this->userRepository->create($data);
        
        // Rolleri ata
        if (!empty($roles)) {
            $user->assignRole($roles);
        }
        
        // Hoş geldin maili gönder
        $this->sendWelcomeEmail($user);
        
        return $user;
    }

    /**
     * Kullanıcı güncelle
     */
    public function updateUser(int $id, array $data): bool
    {
        $user = $this->userRepository->findById($id);
        
        // Rolleri ayır
        $roles = $data['roles'] ?? [];
        unset($data['roles']);
        
        $updated = $this->userRepository->update($id, $data);
        
        // Rolleri güncelle
        if ($updated && !empty($roles)) {
            $user->syncRoles($roles);
        }
        
        if ($updated && isset($data['email']) && $data['email'] !== $user->email) {
            // Email değişikliği varsa bilgilendirme maili gönder
            $this->sendEmailChangeNotification($user, $data['email']);
        }
        
        return $updated;
    }

    /**
     * Kullanıcı sil
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);
        $deleted = $this->userRepository->delete($id);
        
        if ($deleted) {
            // Hesap silme bildirimi gönder
            $this->sendAccountDeletionNotification($user);
        }
        
        return $deleted;
    }

    /**
     * Kullanıcı arama
     */
    public function searchUsers(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->search($query, $perPage);
    }

    /**
     * Kullanıcının rollerini getir
     */
    public function getUserRoles(int $userId): Collection
    {
        $user = $this->userRepository->findById($userId);
        
        return $user ? $user->roles : collect();
    }

    /**
     * Kullanıcının yetkilerini getir
     */
    public function getUserPermissions(int $userId): Collection
    {
        $user = $this->userRepository->findById($userId);
        
        return $user ? $user->getAllPermissions() : collect();
    }

    /**
     * Role sahip kullanıcıları getir
     */
    public function getUsersByRole(string $roleName): Collection
    {
        return $this->userRepository->getModel()->role($roleName)->get();
    }

    /**
     * Yetkiye sahip kullanıcıları getir
     */
    public function getUsersByPermission(string $permissionName): Collection
    {
        return $this->userRepository->getModel()->permission($permissionName)->get();
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

    /**
     * Hesap silme bildirimi gönder
     */
    private function sendAccountDeletionNotification(User $user): void
    {
        $this->mailDispatcher->send([
            'to' => $user->email,
            'subject' => 'Hesabınız Silindi - ' . config('app.name'),
            'content' => "Merhaba {$user->name},\n\n" .
                        "Hesabınız sistemden silinmiştir.\n\n" .
                        "Bu işlem geri alınamaz. Hesabınızla ilgili tüm veriler kalıcı olarak silinmiştir.\n\n" .
                        "Eğer bu işlemi siz yapmadıysanız veya bir hata olduğunu düşünüyorsanız, lütfen bizimle iletişime geçin.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'account_deletion',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'action' => 'user_deleted'
            ]
        ]);
    }
} 