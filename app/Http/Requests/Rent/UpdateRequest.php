<?php

namespace App\Http\Requests\Rent;

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
            'rental_id' => ['sometimes', 'exists:rentals,id'],
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'min:1900'],
            'amount_paid' => ['sometimes', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
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
            'rental_id.exists' => 'The selected rental does not exist.',
            'month.between' => 'The month must be between 1 and 12.',
            'year.min' => 'The year must be a valid value.',
            'amount_paid.numeric' => 'The amount paid must be a number.',
            'payment_date.date' => 'The payment date must be a valid date.',
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
            'rental_id' => 'rental',
            'month' => 'month',
            'year' => 'year',
            'amount_paid' => 'amount paid',
            'payment_date' => 'payment date',
        ];
    }
}
