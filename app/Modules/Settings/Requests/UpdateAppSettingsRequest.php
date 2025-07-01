<?php

namespace App\Modules\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppSettingsRequest extends FormRequest
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
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_logo' => 'nullable|string|max:255',
            'site_favicon' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'site_name.required' => 'Site adı zorunludur.',
            'site_name.max' => 'Site adı en fazla 255 karakter olabilir.',
            'site_description.max' => 'Site açıklaması en fazla 500 karakter olabilir.',
            'site_logo.max' => 'Logo URL\'si en fazla 255 karakter olabilir.',
            'site_favicon.max' => 'Favicon URL\'si en fazla 255 karakter olabilir.',
            'contact_email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'contact_email.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
            'contact_phone.max' => 'Telefon numarası en fazla 20 karakter olabilir.',
            'contact_address.max' => 'Adres en fazla 500 karakter olabilir.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'site_name' => 'Site adı',
            'site_description' => 'Site açıklaması',
            'site_logo' => 'Site logosu',
            'site_favicon' => 'Site favicon',
            'contact_email' => 'İletişim e-posta adresi',
            'contact_phone' => 'İletişim telefon numarası',
            'contact_address' => 'İletişim adresi',
        ];
    }
} 