<?php

namespace App\Http\Requests\Option;

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
            'option_text' => ['nullable', 'string'],
            'is_correct' => ['nullable', 'boolean'],
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
            'option_text.string' => 'The option text must be a valid string.',
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
