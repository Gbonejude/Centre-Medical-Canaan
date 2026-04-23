<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Payment;

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
     * Define validation rules based on the customer type.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => ['required', 'exists:customers,id'],
            'type' => ['required', Rule::enum(CustomerType::class)],
            'payment_date' => ['required', 'date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'amount_due' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:1900'],
        ];

        switch ($this->type) {
            case CustomerType::HOME_CARE_CLIENTS->value:
                $rules['co_pay'] = ['required', 'numeric', 'min:0'];
                break;

            case CustomerType::RESIDENTIAL_CLIENTS->value:
                $rules['rent'] = ['required', 'numeric', 'min:0'];
                break;

            case CustomerType::PRIVATE_CLIENTS->value:
                $rules['private_pay'] = ['required', 'numeric', 'min:0'];
                break;
        }

        return $rules;
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
            'type.required' => 'The customer type is required.',
            'type.enum' => 'The customer type is invalid.',
            'payment_date.required' => 'The payment date is required.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'co_pay.required' => 'The co-pay amount is required for Home Care Clients.',
            'rent.required' => 'The rent amount is required for Residential Clients.',
            'private_pay.required' => 'The private pay amount is required for Private Clients.',
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
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('type')) {
            $total = null;

            switch ($this->type) {
                case CustomerType::HOME_CARE_CLIENTS->value:
                    if ($this->filled('co_pay')) {
                        $total = $this->co_pay;
                    }
                    break;

                case CustomerType::RESIDENTIAL_CLIENTS->value:
                    if ($this->filled('rent')) {
                        $total = $this->rent;
                    }
                    break;

                case CustomerType::PRIVATE_CLIENTS->value:
                    if ($this->filled('private_pay')) {
                        $total = $this->private_pay;
                    }
                    break;
            }

            if ($total !== null) {
                $this->merge(['total' => $total]);
            }
        }
    }
}
