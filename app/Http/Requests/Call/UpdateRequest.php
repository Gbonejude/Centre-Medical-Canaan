<?php

namespace App\Http\Requests\Call;

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
     */
    public function rules(): array
    {
        return [
            'call_time' => ['sometimes', 'date'],
            'title' => ['sometimes', 'string', 'max:255'],
            'respondent_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'staff_comment' => ['sometimes', 'nullable', 'string'],
            'staff_initial' => ['sometimes', 'string'],
            'voice_note' => ['nullable', 'file', 'mimes:webm,mp3,wav,ogg', 'max:10240'], // 10MB max
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'call_time.date' => 'The call time must be a valid date.',
            'title.string' => 'The title must be a valid string.',
            'respondent_name.string' => 'The respondent name must be a valid string.',
            'description.string' => 'The description must be a valid string.',
            'staff_initial.string' => 'The staff initial must be a valid string.',
            'created_by.exists' => 'The selected creator does not exist.',
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'call_time' => 'call time',
            'title' => 'title',
            'respondent_name' => 'respondent name',
            'description' => 'call description',
            'staff_comment' => 'staff comment',
            'staff_initial' => 'staff initial',
            'created_by' => 'creator',
        ];
    }
}
