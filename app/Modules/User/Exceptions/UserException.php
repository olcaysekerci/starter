<?php

namespace App\Modules\User\Exceptions;

use Exception;

class UserException extends Exception
{
    public static function userNotFound(int $id): self
    {
        return new self("Kullanıcı bulunamadı. ID: {$id}");
    }

    public static function emailAlreadyExists(string $email): self
    {
        return new self("Bu e-posta adresi zaten kullanılıyor: {$email}");
    }

    public static function cannotDeleteSelf(): self
    {
        return new self('Kendi hesabınızı silemezsiniz.');
    }

    public static function invalidRole(string $role): self
    {
        return new self("Geçersiz rol: {$role}");
    }

    public static function userInactive(int $id): self
    {
        return new self("Kullanıcı aktif değil. ID: {$id}");
    }

    public static function updateFailed(int $id): self
    {
        return new self("Kullanıcı güncellenirken bir hata oluştu. ID: {$id}");
    }

    public static function deleteFailed(int $id): self
    {
        return new self("Kullanıcı silinirken bir hata oluştu. ID: {$id}");
    }
} 