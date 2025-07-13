<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->route('role'),
            'description' => 'nullable|string|max:500',
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
            'description.max' => 'Açıklama en fazla 500 karakter olabilir.',
            'permissions.array' => 'Yetkiler dizi formatında olmalıdır.',
            'permissions.*.string' => 'Yetki adı metin formatında olmalıdır.',
            'permissions.*.exists' => 'Seçilen yetki mevcut değil.',
        ];
    }
} 