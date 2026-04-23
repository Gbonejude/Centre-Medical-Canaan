<?php

namespace App\Http\Requests\JobApplication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string'],
            'resume_path' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

            Rule::unique('job_applications')
                ->where(fn ($query) => $query
                    ->where('guest_id', $this->input('guest_id'))
                    ->where('job_offer_id', $this->input('job_offer_id'))
                ),

        ];
    }

    /**
     * Custom error messages (optional).
     */
    public function messages(): array
    {
        return [

            'resume_path.required' => 'The resume file is required.',
            'resume_path.mimes' => 'The resume must be a file of type: pdf, doc, docx.',
            'resume_path.max' => 'The resume may not be greater than 5MB.',

            'cover_letter_path.mimes' => 'The cover letter must be a PDF file.',
            'cover_letter_path.max' => 'The cover letter may not be greater than 5MB.',

            'job_applications.unique' => 'You have already applied to this job offer.',

        ];
    }
}
