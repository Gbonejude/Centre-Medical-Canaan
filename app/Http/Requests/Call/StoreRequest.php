<?php

namespace App\Http\Requests\Call;

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
     */
    public function rules(): array
    {
        return [
            'call_time' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'respondent_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'staff_comment' => ['nullable', 'string'],
            'staff_initial' => ['required', 'string'],
            'voice_note' => ['nullable', 'file', 'mimes:webm,mp3,wav,ogg', 'max:10240'], // 10MB max
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'call_time.required' => 'The call time is required.',
            'call_time.date' => 'The call time must be a valid date.',
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a valid string.',
            'respondent_name.required' => 'The respondent name is required.',
            'description.required' => 'The description is required.',
            'staff_initial.required' => 'The staff initial is required.',
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
