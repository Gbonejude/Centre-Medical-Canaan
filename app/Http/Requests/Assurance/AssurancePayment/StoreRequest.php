<?php

declare(strict_types=1);

namespace App\Http\Requests\Assurance\AssurancePayment;

use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'assurance_id' => ['required', 'exists:assurances,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'type' => ['required', Rule::enum(CustomerType::class)],
            'payment_date' => ['required', 'date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'amount_due' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'total' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:1900'],
            'reference' => ['nullable', 'string', 'unique:payments,reference'],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'The customer is required.',
            'customer_id.exists' => 'The selected customer is invalid.',
            'assurance_id.required' => 'The assurance is required.',
            'assurance_id.exists' => 'The selected assurance is invalid.',
            'type.required' => 'The customer type is required.',
            'type.enum' => 'The customer type is invalid.',
            'payment_date.required' => 'The payment date is required.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'total.required' => 'The total amount is required.',
            'total.numeric' => 'The total amount must be a number.',
            'total.min' => 'The total amount must be at least 0.',
            'month.required' => 'The month is required.',
            'month.between' => 'The month must be between 1 and 12.',
            'year.required' => 'The year is required.',
            'year.min' => 'The year must be a valid value.',
            'start_date.required' => 'Service start date is required.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.required' => 'Service end date is required.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'amount_due.required' => 'Amount due is required.',
            'amount_due.numeric' => 'Amount due must be a valid number.',
            'amount_due.min' => 'Amount due must be at least 0.',
            'reference.unique' => 'This reference already exists.',
        ];
    }
}
