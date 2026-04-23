<?php

namespace App\Http\Requests\Expense\ExpenseSubcategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for creating an expense subcategory.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'expense_type_id' => 'required|exists:expense_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'expense_type_id.required' => 'Expense type is required.',
            'expense_type_id.exists' => 'Selected expense type is invalid.',
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
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
            'expense_type_id' => 'expense type',
            'name' => 'subcategory name',
            'description' => 'subcategory description',
        ];
    }
}
