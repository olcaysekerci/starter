<?php

namespace App\Modules\Dashboard\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDashboardRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'layout' => 'nullable|string|in:default,compact,wide',
            'theme' => 'nullable|string|in:light,dark,auto',
            'is_active' => 'nullable|boolean',
            'user_preferences' => 'nullable|array',
            'widgets' => 'nullable|array',
            'charts' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Dashboard başlığı zorunludur.',
            'title.string' => 'Dashboard başlığı metin olmalıdır.',
            'title.max' => 'Dashboard başlığı en fazla 255 karakter olabilir.',
            'description.max' => 'Dashboard açıklaması en fazla 1000 karakter olabilir.',
            'layout.in' => 'Geçersiz layout seçimi.',
            'theme.in' => 'Geçersiz tema seçimi.',
            'is_active.boolean' => 'Aktif durumu geçerli değil.',
            'user_preferences.array' => 'Kullanıcı tercihleri dizi olmalıdır.',
            'widgets.array' => 'Widget\'lar dizi olmalıdır.',
            'charts.array' => 'Grafikler dizi olmalıdır.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'dashboard başlığı',
            'description' => 'dashboard açıklaması',
            'layout' => 'layout',
            'theme' => 'tema',
            'is_active' => 'aktif durumu',
            'user_preferences' => 'kullanıcı tercihleri',
            'widgets' => 'widget\'lar',
            'charts' => 'grafikler',
        ];
    }
} 