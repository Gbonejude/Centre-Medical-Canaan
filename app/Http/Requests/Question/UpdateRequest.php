<?php

namespace App\Http\Requests\Question;

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
            'question_text' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'quizze_id' => ['nullable', 'exists:quizzes,id'],
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
            'question_text.string' => 'The question text must be a valid string.',
            'type.in' => 'The question type must be either "multiple_choice" or "text".',
            'quizze_id.exists' => 'The selected quiz does not exist.',
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
            'question_text' => 'question text',
            'type' => 'question type',
            'quizze_id' => 'quiz',
        ];
    }
}
