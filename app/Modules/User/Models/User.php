<?php

namespace App\Modules\User\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The guard name for the permissions
     */
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'password',
        'status',
        'type',
    ];

    /**
     * The attributes that should be mass assignable only by admins.
     *
     * @var array<int, string>
     */
    protected $adminFillable = [
        'is_active',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'phone',
        'address',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
        'formatted_phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Status enum değerleri
     */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_BANNED = 'banned';
    public const STATUS_DELETED = 'deleted';

    /**
     * Type enum değerleri
     */
    public const TYPE_ADMIN = 'admin';
    public const TYPE_MODERATOR = 'moderator';
    public const TYPE_WEB = 'web';

    /**
     * Tüm status değerlerini getir
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_PENDING_VERIFICATION,
            self::STATUS_SUSPENDED,
            self::STATUS_BANNED,
            self::STATUS_DELETED,
        ];
    }

    /**
     * Tüm type değerlerini getir
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_ADMIN,
            self::TYPE_MODERATOR,
            self::TYPE_WEB,
        ];
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    /**
     * Scope for suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    /**
     * Scope for banned users
     */
    public function scopeBanned($query)
    {
        return $query->where('status', self::STATUS_BANNED);
    }

    /**
     * Scope for pending verification users
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status', self::STATUS_PENDING_VERIFICATION);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmin($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    /**
     * Scope for moderator users
     */
    public function scopeModerator($query)
    {
        return $query->where('type', self::TYPE_MODERATOR);
    }

    /**
     * Scope for web users
     */
    public function scopeWeb($query)
    {
        return $query->where('type', self::TYPE_WEB);
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Check if user is banned
     */
    public function isBanned(): bool
    {
        return $this->status === self::STATUS_BANNED;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * Check if user is moderator
     */
    public function isModerator(): bool
    {
        return $this->type === self::TYPE_MODERATOR;
    }

    /**
     * Check if user is web user
     */
    public function isWeb(): bool
    {
        return $this->type === self::TYPE_WEB;
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the user's first name.
     */
    public function getFirstNameAttribute($value): string
    {
        return $value ?? '';
    }

    /**
     * Get the user's last name.
     */
    public function getLastNameAttribute($value): string
    {
        return $value ?? '';
    }

    /**
     * Get user's initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->full_name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }

        $phone = $this->phone;
        
        // 10 haneli telefon numarasını formatla
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8, 2);
        }
        
        return $phone;
    }
} 