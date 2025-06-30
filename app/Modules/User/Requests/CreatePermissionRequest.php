<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Yetki adı zorunludur.',
            'name.max' => 'Yetki adı en fazla 255 karakter olabilir.',
            'name.unique' => 'Bu yetki adı zaten kullanılıyor.',
            'display_name.required' => 'Görünen ad zorunludur.',
            'display_name.max' => 'Görünen ad en fazla 255 karakter olabilir.',
            'description.max' => 'Açıklama en fazla 500 karakter olabilir.',
            'module.max' => 'Modül adı en fazla 255 karakter olabilir.',
            'guard_name.max' => 'Guard adı en fazla 255 karakter olabilir.',
        ];
    }
} 