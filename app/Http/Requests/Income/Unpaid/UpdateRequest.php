<?php

namespace App\Http\Requests\Income\Unpaid;

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
            'remaining_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'income_id' => ['nullable', 'exists:incomes,id'],
            'note' => ['nullable', 'string'],
            'due_date' => ['sometimes', 'required', 'date'],
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
            'remaining_amount.required' => 'The remaining amount is required when provided.',
            'remaining_amount.numeric' => 'The remaining amount must be a number.',
            'remaining_amount.min' => 'The remaining amount must be at least 0.',
            'income_id.exists' => 'The selected income does not exist.',
            'note.string' => 'The note must be a string.',
            'due_date.required' => 'The due date is required when provided.',
            'due_date.date' => 'The due date must be a valid date.',
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
            'remaining_amount' => 'remaining amount',
            'income_id' => 'related income',
            'note' => 'note',
            'due_date' => 'due date',
        ];
    }
}
