<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'course_type' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'has_certification' => 'boolean',
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
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title must not exceed 255 characters.',
            'description.string' => 'The description must be a valid string.',
            'category_id.exists' => 'The selected category does not exist.',
            'has_certification.boolean' => 'The certification field must be true or false.',
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
            'title' => 'title',
            'description' => 'description',
            'category_id' => 'category',
            'has_certification' => 'certification',
        ];
    }
}
