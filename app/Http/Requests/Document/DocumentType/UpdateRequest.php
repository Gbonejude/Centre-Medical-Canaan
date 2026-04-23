<?php

namespace App\Http\Requests\Document\DocumentType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uuid = $this->route('document_type');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_types', 'name')->ignore($uuid, 'uuid'),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'validity_duration_months' => ['nullable', 'integer', 'min:1', 'max:1200'],
            'reminder_before_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'validity_duration_months' => 'validity duration',
            'reminder_before_days' => 'reminder delay',
            'is_active' => 'active status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The document type name is required.',
            'name.unique' => 'This document type already exists.',
            'name.max' => 'The name must not exceed 255 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
            'validity_duration_months.integer' => 'The validity duration must be a number.',
            'validity_duration_months.min' => 'The validity duration must be at least 1 day.',
            'reminder_before_days.integer' => 'The reminder delay must be a number.',
            'reminder_before_days.min' => 'The reminder delay must be at least 1 day.',
        ];
    }
}
