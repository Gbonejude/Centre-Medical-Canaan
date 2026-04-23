<?php

namespace App\Http\Requests\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'issued_at' => ['nullable', 'date'],
            'certificate_url' => ['nullable', 'url'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'issued_at.date' => 'The issued_at field must be a valid date.',
            'certificate_url.url' => 'The certificate_url field must be a valid URL.',
        ];
    }
}
