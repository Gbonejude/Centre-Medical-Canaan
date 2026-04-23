<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer\Payment;

use App\Enums\CustomerType;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateRequest extends FormRequest
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
            'customer_id' => ['sometimes', 'exists:customers,id'],
            'type' => ['sometimes', Rule::enum(CustomerType::class)],
            'payment_date' => ['sometimes', 'date'],
            'note' => ['nullable', 'string'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer', 'min:1900'],
        ];

        $type = $this->type;
        if (! $type && $this->route('payment')) {
            $paymentUuid = $this->route('payment');
            $payment = $this->findPaymentByUuid($paymentUuid);

            if ($payment && $payment->customer) {
                $type = $payment->customer->type;
            }
        }

        if ($type) {
            switch ($type) {
                case CustomerType::HOME_CARE_CLIENTS->value:
                    $rules['co_pay'] = ['sometimes', 'numeric', 'min:0'];
                    break;

                case CustomerType::RESIDENTIAL_CLIENTS->value:
                    $rules['rent'] = ['sometimes', 'numeric', 'min:0'];
                    break;

                case CustomerType::PRIVATE_CLIENTS->value:
                    $rules['private_pay'] = ['sometimes', 'numeric', 'min:0'];
                    break;
            }
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
            'customer_id.exists' => 'The selected customer is invalid.',
            'type.enum' => 'The customer type is invalid.',
            'payment_date.date' => 'The payment date must be a valid date.',
            'co_pay.numeric' => 'The co-pay amount must be a number.',
            'co_pay.min' => 'The co-pay amount must be at least 0.',
            'rent.numeric' => 'The rent amount must be a number.',
            'rent.min' => 'The rent amount must be at least 0.',
            'private_pay.numeric' => 'The private pay amount must be a number.',
            'private_pay.min' => 'The private pay amount must be at least 0.',
            'total.numeric' => 'The total amount must be a number.',
            'total.min' => 'The total amount must be at least 0.',
            'month.between' => 'The month must be between 1 and 12.',
            'year.min' => 'The year must be a valid value.',
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
        } else {
            if ($this->filled('co_pay')) {
                $this->merge([
                    'type' => CustomerType::HOME_CARE_CLIENTS->value,
                    'total' => $this->co_pay,
                ]);
            } elseif ($this->filled('rent')) {
                $this->merge([
                    'type' => CustomerType::RESIDENTIAL_CLIENTS->value,
                    'total' => $this->rent,
                ]);
            } elseif ($this->filled('private_pay')) {
                $this->merge([
                    'type' => CustomerType::PRIVATE_CLIENTS->value,
                    'total' => $this->private_pay,
                ]);
            }
        }
    }

    /**
     * Find a payment by UUID.
     *
     * @return \App\Models\Payment|null
     */
    private function findPaymentByUuid(string $uuid)
    {
        return Payment::with('customer')
            ->where('uuid', $uuid)
            ->first();
    }
}
