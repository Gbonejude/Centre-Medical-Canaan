<?php

namespace App\Http\Requests\Quiz;

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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_id' => ['required', 'exists:courses,id'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.id' => ['nullable', 'exists:questions,id'],
            'questions.*.question_text' => ['required', 'string'],
            'questions.*.answers' => ['required', 'array', 'min:2'],
            'questions.*.answers.*.id' => ['nullable', 'exists:answers,id'],
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
            'course_id.required' => 'A course must be selected.',
            'course_id.exists' => 'The selected course does not exist.',
            'questions.required' => 'At least one question is required.',
            'questions.array' => 'The questions must be an array.',
            'questions.min' => 'The quiz must have at least one question.',
            'questions.*.question_text.required' => 'Each question must have text.',
            'questions.*.answers.required' => 'Each question must have at least two answers.',
            'questions.*.answers.array' => 'Answers must be an array.',
            'questions.*.answers.min' => 'Each question must have at least two answers.',
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
            'questions.*.question_text' => 'question text',
            'questions.*.answers' => 'question answers',
            'questions.*.answers.*.answer_text' => 'answer text',
            'questions.*.answers.*.is_correct' => 'answer correctness',
        ];
    }
}
