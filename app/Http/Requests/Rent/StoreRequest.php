<?php

namespace App\Http\Requests\Rent;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
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
            'rental_id' => ['required', 'exists:rentals,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:1900'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
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
            'rental_id.required' => 'The rental reference is required.',
            'rental_id.exists' => 'The selected rental does not exist.',
            'month.required' => 'The month is required.',
            'month.between' => 'The month must be between 1 and 12.',
            'year.required' => 'The year is required.',
            'year.min' => 'The year must be a valid value.',
            'amount_paid.required' => 'The amount paid is required.',
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
