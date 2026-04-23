<?php

namespace App\Http\Requests\Course\UserCourse;

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
     * Validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'enrolled_at' => ['nullable', 'date'],
            'user_id' => ['nullable', 'exists:users,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'progress.integer' => 'The progress must be an integer.',
            'progress.min' => 'The progress must be at least 0.',
            'progress.max' => 'The progress must not exceed 100.',
            'user_id.exists' => 'The selected user does not exist.',
            'course_id.exists' => 'The selected course does not exist.',
            'enrolled_at.date' => 'The enrolled at field must be a valid date.',
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
            'progress' => 'progress',
            'user_id' => 'user',
            'course_id' => 'course',
            'enrolled_at' => 'enrolled at',
        ];
    }
}
