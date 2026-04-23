<?php

namespace App\Http\Requests\Option;

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
            'option_text' => ['required', 'string'],
            'is_correct' => ['required', 'boolean'],
            'question_id' => ['nullable', 'exists:questions,id'],
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
            'option_text.required' => 'The option text is required.',
            'option_text.string' => 'The option text must be a valid string.',
            'is_correct.required' => 'The "is_correct" field is required.',
            'is_correct.boolean' => 'The "is_correct" field must be a boolean.',
            'question_id.exists' => 'The selected question does not exist.',
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
            'option_text' => 'option text',
            'is_correct' => 'is correct',
            'question_id' => 'question',
        ];
    }
}
