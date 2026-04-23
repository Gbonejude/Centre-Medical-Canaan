<?php

namespace App\Http\Requests\Rental;

use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        return [
            'property_address' => ['sometimes', 'string', 'max:255'],
            'renter_id' => ['nullable', 'exists:renters,id'],
            'monthly_rent' => ['sometimes', 'numeric', 'min:0'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'property_address.string' => 'The property address must be a valid string.',
            'property_address.max' => 'The property address must not exceed 255 characters.',

            'renter_id.exists' => 'The selected renter does not exist.',

            'monthly_rent.numeric' => 'The monthly rent must be a valid number.',
            'monthly_rent.min' => 'The monthly rent must be at least 0.',

            'start_date.date' => 'The start date must be a valid date.',

            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
        ];
    }

    /**
     * Custom attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'property_address' => 'property address',
            'renter_id' => 'renter',
            'monthly_rent' => 'monthly rent',
            'start_date' => 'start date',
            'end_date' => 'end date',
        ];
    }
}
