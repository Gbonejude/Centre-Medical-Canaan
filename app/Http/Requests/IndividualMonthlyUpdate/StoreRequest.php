<?php

namespace App\Http\Requests\IndividualMonthlyUpdate;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'overall_health_information' => ['nullable', 'string', 'max:10000'],
            'appointments' => ['nullable', 'string', 'max:10000'],
            'social_activities' => ['nullable', 'string', 'max:10000'],
            'condition_medication_change' => ['required', 'boolean'],
            'condition_medication_change_description' => ['nullable', 'string', 'max:5000'],
            'new_behavior' => ['required', 'boolean'],
            'new_behavior_description' => ['nullable', 'string', 'max:5000'],
            'specific_report' => ['required', 'boolean'],
            'specific_report_description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'The customer is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'year.required' => 'The year is required.',
            'month.required' => 'The month is required.',
        ];
    }
}
