<?php

namespace App\Http\Requests\Career;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lastname' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns|unique:candidates,email',
            'birthday' => 'required|date',
            'phone' => ['required', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/', 'unique:candidates,phone'],
            'address' => 'required|string|max:1000',
            'home_phone' => ['nullable', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'alt_phone' => ['nullable', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'social_security_number' => 'nullable|string',
            'gender' => 'required|string',

            'registered_with_lhc_before' => 'required|boolean',
            'lhc_registration_date' => 'nullable|date|required_if:registered_with_lhc_before,true',
            'reason_for_leaving_lhc' => 'nullable|string|max:1000|required_if:registered_with_lhc_before,true',
            'how_did_you_hear_about_us' => 'nullable|string|max:1000',

            'has_valid_drivers_license' => 'required|boolean',
            'drivers_license_state' => 'nullable|string|max:100|required_if:has_valid_drivers_license,true',
            'license_number' => 'nullable|string|max:50|required_if:has_valid_drivers_license,true',

            'educations' => 'required|array|min:1|max:10',
            'educations.*.institution_name' => 'required|string|max:255',
            'educations.*.institution_location' => 'required|string|max:255',
            'educations.*.education_level' => 'required|in:high_school,college,university,certification',
            'educations.*.degree_type' => 'nullable|string|max:100',
            'educations.*.field_of_study' => 'nullable|string|max:255',
            'educations.*.start_date' => 'nullable|date',
            'educations.*.end_date' => 'nullable|date|after_or_equal:educations.*.start_date',
            'educations.*.graduated' => 'required|boolean',
            'educations.*.gpa' => 'nullable|numeric|min:0|max:4.00',
            'educations.*.order' => 'required|integer|min:1',

            'personal_references' => 'required|array|min:1|max:5',
            'personal_references.*.name' => 'required|string|max:255',
            'personal_references.*.phone' => ['required', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'personal_references.*.occupation' => 'required|string|max:255',

            'employment_history' => 'required|array|min:1|max:10',
            'employment_history.*.from_date' => 'required|date',
            'employment_history.*.to_date' => 'nullable|date|after_or_equal:employment_history.*.from_date',
            'employment_history.*.job_title' => 'required|string|max:255',
            'employment_history.*.employer_name' => 'required|string|max:255',
            'employment_history.*.employer_address' => 'required|string|max:1000',
            'employment_history.*.reason_for_leaving' => 'nullable|string|max:1000',
            'employment_history.*.type_of_work_performed' => 'required|string|max:1000',
            'employment_history.*.order' => 'required|integer|min:1',

            'message' => ['nullable', 'string'],
            'career' => ['nullable', 'boolean'],
            'desired_position' => 'required|string|max:255',
            'resume_path' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cover_letter_path' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],

        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lastname.required' => 'Last name is required.',
            'firstname.required' => 'First name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already in use.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already in use.',
            'phone.regex' => 'Phone number format is invalid.',
            'address.required' => 'Address is required.',
            'birthday.required' => 'Birthday is required.',
            'birthday.date' => 'Birthday must be a valid date.',

            'lhc_registration_date.required_if' => 'LHC registration date is required if you were registered before.',
            'reason_for_leaving_lhc.required_if' => 'Reason for leaving LHC is required if you were registered before.',

            'drivers_license_state.required_if' => 'Driver\'s license state is required if you have a valid license.',
            'license_number.required_if' => 'License number is required if you have a valid license.',

            'educations.required' => 'Education information is required.',
            'educations.min' => 'You must provide at least one education entry.',
            'educations.max' => 'You cannot provide more than 10 education entries.',
            'educations.*.institution_name.required' => 'Institution name is required.',
            'educations.*.institution_location.required' => 'Institution location is required.',
            'educations.*.education_level.required' => 'Education level is required.',
            'educations.*.education_level.in' => 'Education level must be one of: high school, college, university, or certification.',
            'educations.*.end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'educations.*.gpa.numeric' => 'GPA must be a number.',
            'educations.*.gpa.max' => 'GPA cannot exceed 4.00.',

            'personal_references.required' => 'At least one personal reference is required.',
            'personal_references.min' => 'You must provide at least one personal reference.',
            'personal_references.max' => 'You cannot provide more than 5 personal references.',
            'personal_references.*.name.required' => 'Reference name is required.',
            'personal_references.*.phone.required' => 'Reference phone number is required.',
            'personal_references.*.phone.regex' => 'Reference phone number format is invalid.',
            'personal_references.*.occupation.required' => 'Reference occupation is required.',

            'employment_history.required' => 'Employment history is required.',
            'employment_history.min' => 'You must provide at least one job in your employment history.',
            'employment_history.max' => 'You cannot provide more than 10 jobs.',
            'employment_history.*.from_date.required' => 'Employment start date is required.',
            'employment_history.*.to_date.after_or_equal' => 'End date must be after or equal to start date.',
            'employment_history.*.job_title.required' => 'Job title is required.',
            'employment_history.*.employer_name.required' => 'Employer name is required.',
            'employment_history.*.employer_address.required' => 'Employer address is required.',
            'employment_history.*.type_of_work_performed.required' => 'Type of work performed is required.',

            'resume_path.mimes' => 'The resume must be a file of type: pdf, doc, docx.',
            'resume_path.max' => 'The resume may not be greater than 5MB.',

            'cover_letter_path.mimes' => 'The cover letter must be a PDF file.',
            'cover_letter_path.max' => 'The cover letter may not be greater than 5MB.',
            'desired_position.required' => 'The desired position is required.',
            'desired_position.string' => 'The desired position must be a string.',

        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'lastname' => 'last name',
            'firstname' => 'first name',
            'middle_name' => 'middle name',
            'email' => 'email address',
            'phone' => 'phone',
            'address' => 'address',
            'birthday' => 'birthday',
            'home_phone' => 'home phone',
            'alt_phone' => 'alternative phone',
            'social_security_number' => 'social security number',
            'registered_with_lhc_before' => 'registered with LHC before',
            'lhc_registration_date' => 'LHC registration date',
            'reason_for_leaving_lhc' => 'reason for leaving LHC',
            'how_did_you_hear_about_us' => 'how did you hear about us',
            'has_valid_drivers_license' => 'valid driver\'s license',
            'drivers_license_state' => 'driver\'s license state',
            'license_number' => 'license number',
            'educations' => 'education',
            'personal_references' => 'personal references',
            'employment_history' => 'employment history',
            'resume_path' => 'resume',
            'cover_letter_path' => 'cover letter',
            'desired_position' => 'desired position',
        ];
    }
}
