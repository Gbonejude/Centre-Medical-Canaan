<?php

declare(strict_types=1);

namespace App\Http\Requests\Assurance\AssurancePayment;

use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRequest extends FormRequest
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
            'assurance_id' => ['sometimes', 'exists:assurances,id'],
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'type' => ['sometimes', Rule::enum(CustomerType::class)],
            'payment_date' => ['sometimes', 'date'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'amount_due' => ['sometimes', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'total' => ['sometimes', 'numeric', 'min:0'],
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'min:1900'],
            'reference' => ['nullable', 'string', 'unique:payments,reference,'.$this->route('uuid').',uuid'],
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
            'customer_id.exists' => 'The selected customer is invalid.',
            'assurance_id.exists' => 'The selected assurance is invalid.',
            'type.enum' => 'The customer type is invalid.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'amount_due.numeric' => 'Amount due must be a valid number.',
            'amount_due.min' => 'Amount due must be at least 0.',
            'total.numeric' => 'The total amount must be a number.',
            'total.min' => 'The total amount must be at least 0.',
            'month.between' => 'The month must be between 1 and 12.',
            'year.min' => 'The year must be a valid value.',
            'reference.unique' => 'This reference already exists.',
        ];
    }
}
