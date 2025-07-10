<?php

namespace App\Modules\ActivityLog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CleanupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
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
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'days.required' => 'Gün sayısı zorunludur.',
            'days.integer' => 'Gün sayısı rakam olmalıdır.',
            'days.min' => 'En az 1 gün olmalıdır.',
            'days.max' => 'En fazla 365 gün olmalıdır.',
        ];
    }
}