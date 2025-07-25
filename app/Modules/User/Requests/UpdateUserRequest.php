<?php

namespace App\Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s\(\)]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'],

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
            'phone.regex' => 'Geçerli bir telefon numarası giriniz.',
            'address.max' => 'Adres en fazla 500 karakter olabilir.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'role_id.exists' => 'Seçilen rol geçersiz.',

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
        // Telefon numarasını formatla
        if ($this->has('phone') && !empty($this->phone)) {
            $phone = $this->phone;
            
            // Sadece rakamları al
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Türkiye telefon numarası formatı
            if (strlen($phone) === 12 && substr($phone, 0, 2) === '90') {
                // +90 ile başlıyorsa, 90'ı kaldır
                $phone = substr($phone, 2);
            } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
                // 0 ile başlıyorsa, 0'ı kaldır
                $phone = substr($phone, 1);
            }
            
            // Eğer 10 haneli değilse, geçersiz say
            if (strlen($phone) !== 10) {
                $phone = null;
            }
            
            $this->merge(['phone' => $phone]);
        }
        
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
} 