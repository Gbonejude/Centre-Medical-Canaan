<?php

namespace App\Http\Requests\IndividualMonthlyUpdate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'required', 'integer', 'exists:customers,id'],
            'year' => ['sometimes', 'required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['sometimes', 'required', 'integer', 'min:1', 'max:12'],
            'overall_health_information' => ['nullable', 'string', 'max:10000'],
            'appointments' => ['nullable', 'string', 'max:10000'],
            'social_activities' => ['nullable', 'string', 'max:10000'],
            'condition_medication_change' => ['sometimes', 'required', 'boolean'],
            'condition_medication_change_description' => ['nullable', 'string', 'max:5000'],
            'new_behavior' => ['sometimes', 'required', 'boolean'],
            'new_behavior_description' => ['nullable', 'string', 'max:5000'],
            'specific_report' => ['sometimes', 'required', 'boolean'],
            'specific_report_description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
