<?php

namespace App\Http\Requests\IndividualReport;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],

            'reports' => ['required', 'array', 'min:1'],
            'reports.*.report_field' => ['required', 'string', 'max:255'],
            'reports.*.observation' => ['nullable', 'string', 'max:5000'],
            'reports.*.follow_up_need' => ['nullable', 'string', 'max:1000'],
            'reports.*.report_date' => ['required', 'date'],
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
            'customer_id.required' => 'The customer is required.',
            'customer_id.integer' => 'The customer ID must be a valid integer.',
            'customer_id.exists' => 'The selected customer does not exist.',

            'year.required' => 'The year is required.',
            'year.integer' => 'The year must be a valid integer.',
            'year.min' => 'The year must be at least 2000.',
            'year.max' => 'The year must not exceed 2100.',

            'month.required' => 'The month is required.',
            'month.integer' => 'The month must be a valid integer.',
            'month.min' => 'The month must be between 1 and 12.',
            'month.max' => 'The month must be between 1 and 12.',

            'reports.required' => 'At least one report entry is required.',
            'reports.array' => 'Reports must be an array.',
            'reports.min' => 'At least one report entry is required.',

            'reports.*.report_field.required' => 'Each report must have a field name.',
            'reports.*.report_field.string' => 'The report field must be a valid string.',
            'reports.*.report_field.max' => 'The report field must not exceed 255 characters.',
            'reports.*.observation.string' => 'The observation must be a valid string.',
            'reports.*.observation.max' => 'The observation must not exceed 5000 characters.',
            'reports.*.follow_up_need.string' => 'The follow-up need must be a valid string.',
            'reports.*.follow_up_need.max' => 'The follow-up need must not exceed 1000 characters.',
            'reports.*.report_date.required' => 'The report date is required for each entry.',
            'reports.*.report_date.date' => 'The report date must be a valid date.',
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
            'customer_id' => 'customer',
            'year' => 'year',
            'month' => 'month',
            'reports' => 'report entries',
            'reports.*.report_field' => 'report field',
            'reports.*.observation' => 'observation',
            'reports.*.follow_up_need' => 'follow-up need',
            'reports.*.report_date' => 'report date',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->customer_id) {
                $customer = \App\Models\Customer::find($this->customer_id);
                if ($customer && in_array($customer->type, [\App\Enums\CustomerType::HOME_CARE_CLIENTS, \App\Enums\CustomerType::PRIVATE_CLIENTS])) {
                    $validator->errors()->add(
                        'customer_id',
                        'Individual reports are not available for Home Care or Private clients.'
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('reports') && is_array($this->reports)) {
            $cleanedReports = collect($this->reports)
                ->filter(fn ($report) => ! empty($report['report_field']))
                ->values()
                ->toArray();

            $this->merge([
                'reports' => $cleanedReports,
            ]);
        }
    }
}
