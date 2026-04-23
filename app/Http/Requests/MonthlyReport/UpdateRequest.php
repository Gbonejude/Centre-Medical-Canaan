<?php

namespace App\Http\Requests\MonthlyReport;

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
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
            'report_field' => ['sometimes', 'required', 'string', 'max:255'],
            'observation' => ['nullable', 'string', 'max:5000'],
            'follow_up_need' => ['nullable', 'string', 'max:1000'],
            'year' => ['sometimes', 'required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['sometimes', 'required', 'integer', 'min:1', 'max:12'],
            'report_date' => ['sometimes', 'date'],
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
            'customer_id.required' => 'Please select a client.',
            'customer_id.exists' => 'The selected client is invalid.',
            'report_field.required' => 'Please enter a report field name.',
            'report_field.string' => 'The report field must be a valid string.',
            'report_field.max' => 'The report field must not exceed 255 characters.',
            'observation.string' => 'The observation must be a valid string.',
            'observation.max' => 'The observation must not exceed 5000 characters.',
            'follow_up_need.string' => 'The follow-up need must be a valid string.',
            'follow_up_need.max' => 'The follow-up need must not exceed 1000 characters.',
            'year.required' => 'The year is required.',
            'year.integer' => 'The year must be a valid integer.',
            'year.min' => 'The year must be at least 2000.',
            'year.max' => 'The year must not exceed 2100.',
            'month.required' => 'The month is required.',
            'month.integer' => 'The month must be a valid integer.',
            'month.min' => 'The month must be between 1 and 12.',
            'month.max' => 'The month must be between 1 and 12.',
            'report_date.date' => 'The report date must be a valid date.',
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
            'customer_id' => 'client',
            'report_field' => 'report field',
            'observation' => 'observation',
            'follow_up_need' => 'follow-up need',
            'year' => 'year',
            'month' => 'month',
            'report_date' => 'report date',
        ];
    }
}
