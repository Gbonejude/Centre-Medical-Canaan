<?php

namespace App\Http\Requests\QuarterlyReport;

use Illuminate\Foundation\Http\FormRequest;

class SignReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_signature' => 'required|string',
            'signed_by' => 'required|in:self,parent,guardian',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_signature.required' => 'Please provide your signature.',
            'signed_by.required' => 'Please specify who is signing the report.',
            'signed_by.in' => 'Signed by must be either self, parent, or guardian.',
        ];
    }
}
