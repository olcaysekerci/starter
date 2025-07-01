<?php

namespace App\Modules\MailNotification\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CleanupLogsRequest extends FormRequest
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
            'days' => 'required|integer|min:1|max:365',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'days.required' => 'Gün sayısı zorunludur.',
            'days.integer' => 'Gün sayısı tam sayı olmalıdır.',
            'days.min' => 'Gün sayısı en az 1 olmalıdır.',
            'days.max' => 'Gün sayısı en fazla 365 olabilir.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'days' => 'gün sayısı',
        ];
    }
} 