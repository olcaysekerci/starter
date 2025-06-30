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
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Rol adı zorunludur.',
            'name.max' => 'Rol adı en fazla 255 karakter olabilir.',
            'name.unique' => 'Bu rol adı zaten kullanılıyor.',
            'display_name.required' => 'Görünen ad zorunludur.',
            'display_name.max' => 'Görünen ad en fazla 255 karakter olabilir.',
            'description.max' => 'Açıklama en fazla 500 karakter olabilir.',
            'guard_name.max' => 'Guard adı en fazla 255 karakter olabilir.',
            'permissions.array' => 'Yetkiler dizi formatında olmalıdır.',
            'permissions.*.string' => 'Yetki adı metin formatında olmalıdır.',
            'permissions.*.exists' => 'Seçilen yetki mevcut değil.',
        ];
    }
} 