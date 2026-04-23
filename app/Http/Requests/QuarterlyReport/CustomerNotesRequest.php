<?php

namespace App\Http\Requests\QuarterlyReport;

use Illuminate\Foundation\Http\FormRequest;

class CustomerNotesRequest extends FormRequest
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
            'customer_notes' => 'required|string|max:10000',
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
            'customer_notes.required' => 'Please enter your notes or comments.',
            'customer_notes.max' => 'Notes cannot exceed 10000 characters.',
        ];
    }
}
