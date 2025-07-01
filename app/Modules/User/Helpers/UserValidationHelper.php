<?php

namespace App\Modules\User\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserValidationHelper
{
    /**
     * Email benzersizlik kontrolü
     */
    public static function validateEmailUnique(string $email, ?int $excludeUserId = null): bool
    {
        $rules = ['email' => 'required|email|unique:users,email'];
        
        if ($excludeUserId) {
            $rules['email'] = "required|email|unique:users,email,{$excludeUserId}";
        }
        
        $validator = Validator::make(['email' => $email], $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    /**
     * Şifre güvenlik kontrolü
     */
    public static function validatePasswordStrength(string $password): bool
    {
        $validator = Validator::make(['password' => $password], [
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ]
        ], [
            'password.regex' => 'Şifre en az bir küçük harf, bir büyük harf, bir rakam ve bir özel karakter içermelidir.'
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    /**
     * Telefon numarası formatı kontrolü
     */
    public static function validatePhoneFormat(?string $phone): bool
    {
        if (empty($phone)) {
            return true;
        }
        
        $validator = Validator::make(['phone' => $phone], [
            'phone' => 'regex:/^[\+]?[0-9\s\-\(\)]+$/'
        ], [
            'phone.regex' => 'Geçerli bir telefon numarası giriniz.'
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    /**
     * Rol geçerlilik kontrolü
     */
    public static function validateRoles(array $roles): bool
    {
        $validator = Validator::make(['roles' => $roles], [
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name'
        ], [
            'roles.*.exists' => 'Seçilen rol geçersiz.'
        ]);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return true;
    }

    /**
     * Kullanıcı aktiflik kontrolü
     */
    public static function validateUserActive(int $userId): bool
    {
        $user = \App\Modules\User\Models\User::find($userId);
        
        if (!$user || !$user->is_active) {
            throw new \Exception('Kullanıcı aktif değil.');
        }
        
        return true;
    }
} 