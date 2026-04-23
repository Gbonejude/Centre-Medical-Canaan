<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

final class StoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_year' => ['required'],
            'scheduled_month' => ['required'],
            'course_type' => ['required'],
            'category_id' => ['required', 'exists:categories,id'],
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
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title must not exceed 255 characters.',
            'description.required' => 'The description is required.',
            'scheduled_year.required' => 'The year is required.',
            'scheduled_month.required' => 'The month must be a valid string.',
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
            'scheduled_year' => 'year',
            'scheduled_month' => 'month',
            'has_certification' => 'certification',
        ];
    }
}
