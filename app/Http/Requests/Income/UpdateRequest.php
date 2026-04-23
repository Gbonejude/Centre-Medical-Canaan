<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string'],
            'amount_due' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0', 'lte:amount_due'],
            'income_type_id' => ['nullable', 'exists:income_types,id'],
            'date' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_due.required' => 'The amount due is required.',
            'amount_due.numeric' => 'The amount due must be a number.',
            'amount_due.min' => 'The amount due must be at least 0.',

            'amount_paid.required' => 'The amount paid is required.',
            'amount_paid.numeric' => 'The amount paid must be a number.',
            'amount_paid.min' => 'The amount paid must be at least 0.',
            'amount_paid.lte' => 'The amount paid cannot exceed the amount due.',

            'income_type_id.exists' => 'The selected income type is invalid.',
            'date.required' => 'The income date is required.',
            'date.date' => 'The income date must be valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'income description',
            'amount_due' => 'amount due',
            'amount_paid' => 'amount paid',
            'income_type_id' => 'income type',
            'date' => 'income date',
            'note' => 'note',
        ];
    }
}
