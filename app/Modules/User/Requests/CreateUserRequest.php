<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Ad alanı zorunludur.',
            'first_name.string' => 'Ad alanı metin olmalıdır.',
            'first_name.max' => 'Ad alanı en fazla 255 karakter olabilir.',
            'last_name.required' => 'Soyad alanı zorunludur.',
            'last_name.string' => 'Soyad alanı metin olmalıdır.',
            'last_name.max' => 'Soyad alanı en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
            'address.max' => 'Adres en fazla 500 karakter olabilir.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'roles.array' => 'Roller dizi olmalıdır.',
            'roles.*.exists' => 'Seçilen rol geçersiz.',
            'permissions.array' => 'İzinler dizi olmalıdır.',
            'permissions.*.exists' => 'Seçilen izin geçersiz.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'ad',
            'email' => 'e-posta',
            'password' => 'şifre',
            'phone' => 'telefon',
            'address' => 'adres',
            'roles' => 'roller',
            'is_active' => 'aktif durumu',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
} 