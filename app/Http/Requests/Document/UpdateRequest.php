<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
            'expiry_date' => ['required', 'date', 'after:issue_date'],
            'reminder_date' => ['required', 'date', 'before_or_equal:expiry_date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'document_type_id' => ['required', 'exists:document_types,id'],
            'user_id' => ['required', 'exists:users,id'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png', 'max:10240'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['exists:media,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'issue_date' => 'issue date',
            'expiry_date' => 'expiry date',
            'reminder_date' => 'reminder date',
            'notes' => 'notes',
            'document_type_id' => 'document type',
            'user_id' => 'user',
        ];
    }

    public function messages(): array
    {
        return [
            'issue_date.required' => 'The issue date is required.',
            'issue_date.before_or_equal' => 'The issue date cannot be in the future.',
            'expiry_date.required' => 'The expiry date is required.',
            'expiry_date.after' => 'The expiry date must be after the issue date.',
            'reminder_date.required' => 'The reminder date is required.',
            'reminder_date.before_or_equal' => 'The reminder date must be before or equal to the expiry date.',
            'reminder_date.after_or_equal' => 'The reminder date must be today or in the future.',
            'document_type_id.required' => 'Please select a document type.',
            'document_type_id.exists' => 'The selected document type is invalid.',
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user is invalid.',
        ];
    }
}
