<?php

namespace App\Http\Requests\QuarterlyReport;

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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'provider_id' => ['nullable', 'integer', 'exists:providers,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'medical_appointments' => ['nullable', 'string', 'max:10000'],

            'outcomes' => ['nullable', 'array'],
            'outcomes.*.id' => ['nullable', 'integer', 'exists:quarterly_report_outcomes,id'],
            'outcomes.*.outcome_number' => ['required', 'integer', 'min:1'],
            'outcomes.*.description' => ['required', 'string', 'max:10000'],
            'outcomes.*.start_date' => ['nullable', 'date'],
            'outcomes.*.end_date' => ['nullable', 'date'],
            'outcomes.*.status' => ['required', 'in:achieved,on_track,limited_progress'],
            'outcomes.*.progress_details' => ['required', 'string', 'max:10000'],
            'outcomes.*.plan_change_needed' => ['required', 'boolean'],
            'outcomes.*.plan_change_description' => ['nullable', 'required_if:outcomes.*.plan_change_needed,true', 'string', 'max:10000'],
            'outcomes.*.order' => ['nullable', 'integer', 'min:0'],
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

            'start_date.required' => 'The start date is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'end_date.required' => 'The end date is required.',
            'end_date.date' => 'The end date must be a valid date.',
            'end_date.after' => 'The end date must be after the start date.',

            'outcomes.array' => 'Outcomes must be an array.',
            'outcomes.*.id.exists' => 'The selected outcome does not exist.',
            'outcomes.*.outcome_number.required' => 'Each outcome must have a number.',
            'outcomes.*.outcome_number.integer' => 'The outcome number must be a valid integer.',
            'outcomes.*.description.required' => 'Each outcome must have a description.',
            'outcomes.*.description.max' => 'The outcome description must not exceed 10000 characters.',
            'outcomes.*.start_date.required' => 'Each outcome must have a start date.',
            'outcomes.*.end_date.required' => 'Each outcome must have an end date.',
            'outcomes.*.status.required' => 'Each outcome must have a status.',
            'outcomes.*.status.in' => 'The outcome status must be achieved, on_track, or limited_progress.',
            'outcomes.*.progress_details.required' => 'Each outcome must have progress details.',
            'outcomes.*.plan_change_needed.required' => 'Each outcome must specify if plan change is needed.',
            'outcomes.*.plan_change_description.required_if' => 'Plan change description is required when plan change is needed.',
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
            'start_date' => 'start date',
            'end_date' => 'end date',
            'medical_appointments' => 'medical appointments',
            'outcomes' => 'outcomes',
            'outcomes.*.outcome_number' => 'outcome number',
            'outcomes.*.description' => 'outcome description',
            'outcomes.*.start_date' => 'outcome start date',
            'outcomes.*.end_date' => 'outcome end date',
            'outcomes.*.status' => 'outcome status',
            'outcomes.*.progress_details' => 'progress details',
            'outcomes.*.plan_change_needed' => 'plan change needed',
            'outcomes.*.plan_change_description' => 'plan change description',
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
                        'Quarterly reports are not available for Home Care or Private clients.'
                    );
                }
            }
        });
    }
}
