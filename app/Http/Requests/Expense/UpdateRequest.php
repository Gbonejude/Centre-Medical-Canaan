<?php

namespace App\Http\Requests\Expense;

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
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'date' => ['sometimes', 'required', 'date'],
            'expense_type_id' => ['nullable', 'exists:expense_types,id'],
            'care_house_id' => ['nullable', 'exists:care_houses,id'],
            'expense_subcategory_id' => ['nullable', 'exists:expense_subcategories,id'],

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
            'amount.required' => 'The amount is required when provided.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'description.string' => 'The description must be a valid string.',
            'date.required' => 'The date is required when provided.',
            'date.date' => 'The date must be a valid date.',
            'expense_type_id.exists' => 'The selected expense type is invalid.',
            'care_house_id.exists' => 'The selected group home is invalid.',
            'expense_subcategory_id.exists' => 'The selected expense subcategory is invalid.',

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
            'amount' => 'expense amount',
            'description' => 'expense description',
            'date' => 'expense date',
            'expense_type_id' => 'expense type',
            'expense_subcategory_id' => 'expense subcategory',
            'care_house_id' => 'group home',

        ];
    }
}
