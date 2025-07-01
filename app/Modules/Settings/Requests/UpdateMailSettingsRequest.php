<?php

namespace App\Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMailSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware'de kontrol ediliyor
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'driver' => 'required|string|in:smtp,mailpit,log,array',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|in:tls,ssl,null',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'enabled' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'driver.required' => 'Mail sürücüsü zorunludur.',
            'driver.in' => 'Geçersiz mail sürücüsü.',
            'host.required' => 'SMTP sunucu adresi zorunludur.',
            'host.max' => 'SMTP sunucu adresi en fazla 255 karakter olabilir.',
            'port.required' => 'SMTP port numarası zorunludur.',
            'port.integer' => 'Port numarası sayı olmalıdır.',
            'port.min' => 'Port numarası en az 1 olmalıdır.',
            'port.max' => 'Port numarası en fazla 65535 olabilir.',
            'username.max' => 'Kullanıcı adı en fazla 255 karakter olabilir.',
            'password.max' => 'Şifre en fazla 255 karakter olabilir.',
            'encryption.in' => 'Geçersiz şifreleme türü.',
            'from_address.required' => 'Gönderen e-posta adresi zorunludur.',
            'from_address.email' => 'Geçerli bir e-posta adresi giriniz.',
            'from_address.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
            'from_name.required' => 'Gönderen adı zorunludur.',
            'from_name.max' => 'Gönderen adı en fazla 255 karakter olabilir.',
            'enabled.boolean' => 'Aktif durumu geçersiz.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'driver' => 'Mail sürücüsü',
            'host' => 'SMTP sunucu adresi',
            'port' => 'SMTP port',
            'username' => 'SMTP kullanıcı adı',
            'password' => 'SMTP şifre',
            'encryption' => 'SMTP şifreleme',
            'from_address' => 'Gönderen e-posta adresi',
            'from_name' => 'Gönderen adı',
            'enabled' => 'Mail sistemi aktif',
        ];
    }
} 