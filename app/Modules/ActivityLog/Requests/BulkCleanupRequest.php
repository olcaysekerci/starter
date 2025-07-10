<?php

namespace App\Modules\ActivityLog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkCleanupRequest extends FormRequest
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
            'activity_ids' => 'required|array|min:1',
            'activity_ids.*' => 'required|integer|exists:activity_log,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'activity_ids.required' => 'En az bir aktivite seçilmelidir.',
            'activity_ids.array' => 'Aktivite listesi dizi formatında olmalıdır.',
            'activity_ids.min' => 'En az bir aktivite seçilmelidir.',
            'activity_ids.*.required' => 'Her aktivite ID\'si zorunludur.',
            'activity_ids.*.integer' => 'Aktivite ID\'si rakam olmalıdır.',
            'activity_ids.*.exists' => 'Seçilen aktivite bulunamadı.',
        ];
    }
}