<?php

namespace App\Http\Requests\Expense\ExpenseType;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'authorized_permission_ids' => ['nullable', 'array'],
            'authorized_permission_ids.*' => ['exists:permissions,id'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The expense type name is required when provided.',
            'name.string' => 'The expense type name must be a valid string.',
            'name.max' => 'The expense type name must not exceed 255 characters.',
        ];
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'expense type name',
        ];
    }
}
