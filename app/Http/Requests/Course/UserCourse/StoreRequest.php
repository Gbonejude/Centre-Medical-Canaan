<?php

namespace App\Http\Requests\Course\UserCourse;

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
     * Validation rules for the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'enrolled_at' => ['nullable', 'date'],
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
            'user_id.required' => 'The user ID is required.',
            'course_id.required' => 'The course ID is required.',
            'progress.integer' => 'The progress must be an integer.',
            'progress.min' => 'The progress must be greater than or equal to 0.',
            'progress.max' => 'The progress cannot exceed 100.',
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
