<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Actions\UpdateUserAction;
use App\Modules\MailNotification\Services\MailDispatcherService;
use App\Modules\User\Exceptions\UserException;
use App\Traits\TransactionTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class UserService
{
    use TransactionTrait;

    public function __construct(
        private UserRepository $userRepository,
        private MailDispatcherService $mailDispatcher,
        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction
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
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw UserException::userNotFound($id);
        }
        
        return $user;
    }

    /**
     * Kullanıcı detayını DTO olarak getir
     */
    public function getUserDTOById(int $id): ?UserDTO
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw UserException::userNotFound($id);
        }
        
        return UserDTO::fromModel($user);
    }

    /**
     * Kullanıcı oluştur
     */
    public function create(array $data): User
    {
        return $this->transaction(function () use ($data) {
            $user = $this->userRepository->create($data);
            
            Log::info('Kullanıcı oluşturuldu', [
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);
            
            return $user;
        });
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(User $user, array $data): User
    {
        return $this->transaction(function () use ($user, $data) {
            $user = $this->userRepository->update($user->id, $data);
            
            Log::info('Kullanıcı güncellendi', [
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);
            
            return $user;
        });
    }

    /**
     * Kullanıcı sil
     */
    public function deleteUser(int $id): bool
    {
        return $this->deleteInTransaction(function() use ($id) {
            $user = $this->userRepository->findById($id);
            
            if (!$user) {
                throw new ModelNotFoundException('Kullanıcı bulunamadı.');
            }
            
            // Kendini silmeye çalışıyorsa engelle
            if ($user->id === auth()->id()) {
                throw new ValidationException(
                    Validator::make([], []),
                    'Kendi hesabınızı silemezsiniz.'
                );
            }
            
            $deleted = $this->userRepository->delete($id);
        
            if (!$deleted) {
                throw new \Exception('Kullanıcı silinirken bir hata oluştu.');
            }
            
            // Hesap silme bildirimi gönder
            $this->sendAccountDeletionNotification($user);
            
            Log::info('Kullanıcı silindi', [
                'user_id' => $id,
                'deleted_by' => auth()->id()
            ]);
            
            return true;
        }, 'kullanıcı silme');
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
     * Status'e göre kullanıcıları getir
     */
    public function getUsersByStatus(string $status): Collection
    {
        return $this->userRepository->getModel()->where('status', $status)->get();
    }

    /**
     * Type'a göre kullanıcıları getir
     */
    public function getUsersByType(string $type): Collection
    {
        return $this->userRepository->getModel()->where('type', $type)->get();
    }

    /**
     * Kullanıcı status'ünü güncelle
     */
    public function updateUserStatus(int $userId, string $status): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return false;
        }

        return $user->update(['status' => $status]);
    }

    /**
     * Kullanıcı type'ını güncelle
     */
    public function updateUserType(int $userId, string $type): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            return false;
        }

        return $user->update(['type' => $type]);
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