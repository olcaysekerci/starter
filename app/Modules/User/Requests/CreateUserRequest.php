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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ad alanı zorunludur.',
            'name.string' => 'Ad metin formatında olmalıdır.',
            'name.max' => 'Ad en fazla 255 karakter olabilir.',
            
            'email.required' => 'E-posta alanı zorunludur.',
            'email.string' => 'E-posta metin formatında olmalıdır.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta en fazla 255 karakter olabilir.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            
            'password.required' => 'Şifre alanı zorunludur.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            
            'phone.string' => 'Telefon numarası metin formatında olmalıdır.',
            'phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
            'phone.regex' => 'Geçerli bir telefon numarası giriniz.',
            
            'address.string' => 'Adres metin formatında olmalıdır.',
            'address.max' => 'Adres en fazla 500 karakter olabilir.',
            
            'roles.array' => 'Roller dizi formatında olmalıdır.',
            'roles.*.string' => 'Rol adı metin formatında olmalıdır.',
            'roles.*.exists' => 'Seçilen rol geçersiz.',
            
            'is_active.boolean' => 'Aktif durumu geçersiz.',
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