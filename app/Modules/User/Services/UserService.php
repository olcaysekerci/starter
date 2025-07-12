<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Actions\UpdateUserAction;
use App\Modules\MailNotification\Services\MailNotificationService;
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
        private MailNotificationService $mailNotificationService,
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
     * Kullanıcı detayını genişletilmiş bilgilerle getir
     */
    public function getUserDetailWithStats(int $id): array
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            throw UserException::userNotFound($id);
        }

        // Kullanıcının aktivite loglarını al
        $activityLogs = \App\Modules\ActivityLog\Models\Activity::where('causer_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Son giriş bilgisi
        $lastLogin = $activityLogs->where('description', 'Giriş yaptı')->first();

        // Toplam giriş sayısı
        $totalLogins = \App\Modules\ActivityLog\Models\Activity::where('causer_id', $id)
            ->where('description', 'Giriş yaptı')
            ->count();

        // Son aktivite tarihi
        $lastActivity = $activityLogs->first();

        // Hesap yaşı
        $accountAge = $user->created_at ? $user->created_at->diffInDays(now()) : 0;

        return [
            'user' => UserDTO::fromModel($user),
            'stats' => [
                'total_logins' => $totalLogins,
                'last_login' => $lastLogin ? $lastLogin->created_at->toISOString() : null,
                'last_activity' => $lastActivity ? $lastActivity->created_at->toISOString() : null,
                'account_age_days' => (int) $accountAge,
                'recent_activities' => $activityLogs->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'created_at' => $activity->created_at->toISOString(),
                        'event' => $activity->event,
                        'resolved_event' => $activity->resolved_event,
                    ];
                })
            ]
        ];
    }

    /**
     * Kullanıcı oluştur
     */
    public function create(array $data): User
    {
        return $this->transaction(function () use ($data) {
            $user = $this->userRepository->create($data);
            return $user;
        });
    }

    /**
     * Kullanıcı güncelle
     */
    public function update(User $user, array $data): User
    {
        return $this->transaction(function () use ($user, $data) {
            $updatedUser = $this->userRepository->update($user, $data);
            return $updatedUser;
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
            
            // Aktivite log kaydet
            activity('user_management')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'deleted_user' => [
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'id' => $user->id
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Kullanıcı hesabı silindi');
            
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
        return $this->userRepository->getAll()->filter(function ($user) use ($roleName) {
            return $user->hasRole($roleName);
        });
    }

    /**
     * Yetkiye sahip kullanıcıları getir
     */
    public function getUsersByPermission(string $permissionName): Collection
    {
        return $this->userRepository->getAll()->filter(function ($user) use ($permissionName) {
            return $user->hasPermissionTo($permissionName);
        });
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

        $oldStatus = $user->status;
        $result = $user->update(['status' => $status]);
        
        if ($result) {
            // Aktivite log kaydet
            activity('user_management')
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'status_change' => [
                        'old_status' => $oldStatus,
                        'new_status' => $status
                    ],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ])
                ->log('Kullanıcı durumu güncellendi');
        }

        return $result;
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
        $this->mailNotificationService->send([
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

    /**
     * Email değişikliği bildirimi gönder
     */
    private function sendEmailChangeNotification(User $user, string $newEmail): void
    {
        $this->mailNotificationService->send([
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

    /**
     * Hesap silme bildirimi gönder
     */
    private function sendAccountDeletionNotification(User $user): void
    {
        $this->mailNotificationService->send([
            'to' => $user->email,
            'subject' => 'Hesabınız Silindi - ' . config('app.name'),
            'content' => "Merhaba {$user->full_name},\n\n" .
                        "Hesabınız sistemden silinmiştir.\n\n" .
                        "Bu işlem geri alınamaz. Hesabınızla ilgili tüm veriler kalıcı olarak silinmiştir.\n\n" .
                        "Eğer bu işlemi siz yapmadıysanız veya bir hata olduğunu düşünüyorsanız, lütfen bizimle iletişime geçin.\n\n" .
                        "Saygılarımızla,\n" . config('app.name') . " Ekibi",
            'type' => 'account_deletion',
            'metadata' => [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'user_email' => $user->email,
                'action' => 'user_deleted'
            ]
        ]);
    }
} 