<?php

namespace App\Http\Requests\Quiz;

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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.answers' => ['required', 'array', 'min:2'],
            'questions.*.answers.*.answer_text' => ['required', 'string'],
            'questions.*.answers.*.is_correct' => ['required', 'boolean'],
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
            'title.required' => 'The quiz title is required.',
            'title.string' => 'The quiz title must be a valid string.',
            'title.max' => 'The quiz title must not exceed 255 characters.',
            'description.string' => 'The description must be a valid string.',
            'order.integer' => 'The order must be an integer.',
            'order.min' => 'The order must be a non-negative number.',
            'course_id.exists' => 'The selected module does not exist.',
            'questions.required' => 'At least one question is required.',
            'questions.array' => 'The questions must be an array.',
            'questions.*.question_text.required' => 'Each question must have text.',
            'questions.*.answers.required' => 'Each question must have at least two answers.',
            'questions.*.answers.array' => 'Answers must be an array.',
            'questions.*.answers.*.answer_text.required' => 'Each answer must have text.',
            'questions.*.answers.*.is_correct.required' => 'Each answer must specify if it is correct or not.',
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
            'title' => 'quiz title',
            'description' => 'quiz description',
            'course_id' => 'course',
            'questions' => 'quiz questions',
            'questions.*.text' => 'question text',
            'questions.*.answers' => 'question answers',
            'questions.*.answers.*.answer_text' => 'answer text',
            'questions.*.answers.*.is_correct' => 'answer correctness',
        ];
    }
}
