<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::in(['client', 'caregiver'])],
            'potential_person_name' => ['sometimes', 'string', 'max:255'],
            'potential_person_address' => ['nullable', 'string', 'max:255'],
            'potential_person_phone' => ['sometimes', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],

            'relationship' => ['sometimes', Rule::in(['family_member', 'friend', 'agency', 'other'])],
            'relationship_other' => ['nullable', 'string', 'max:255', 'required_if:relationship,other'],

            'availability' => ['sometimes', Rule::in(['morning', 'evening', 'other'])],
            'availability_other' => ['nullable', 'string', 'max:255', 'required_if:availability,other'],

            'potential_person_information' => ['nullable', 'string'],

            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'The referral type must be either "client" or "caregiver".',

            'potential_person_name.max' => 'The name may not be greater than 255 characters.',
            'potential_person_phone.max' => 'The phone number may not be greater than 20 characters.',

            'relationship.in' => 'Relationship must be one of: family member, friend, agency, or other.',
            'relationship_other.required_if' => 'Please specify the relationship if you selected "other".',

            'availability.in' => 'Availability must be one of: morning, evening, or other.',
            'availability_other.required_if' => 'Please specify the availability if you selected "other".',

            'user_id.exists' => 'The selected user is invalid.',
        ];
    }
}
