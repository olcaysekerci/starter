<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Rol adı zorunludur.',
            'name.max' => 'Rol adı en fazla 255 karakter olabilir.',
            'name.unique' => 'Bu rol adı zaten kullanılıyor.',
            'description.max' => 'Açıklama en fazla 500 karakter olabilir.',
            'guard_name.max' => 'Guard adı en fazla 255 karakter olabilir.',
            'permissions.array' => 'Yetkiler dizi formatında olmalıdır.',
            'permissions.*.integer' => 'Yetki ID\'si sayı formatında olmalıdır.',
            'permissions.*.exists' => 'Seçilen yetki mevcut değil.',
        ];
    }
} 