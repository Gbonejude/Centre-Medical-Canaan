<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(['client', 'caregiver'])],
            'potential_person_name' => ['required', 'string', 'max:255'],
            'potential_person_address' => ['nullable', 'string', 'max:255'],
            'potential_person_phone' => ['required', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'relationship' => ['required', Rule::in(['family_member', 'friend', 'agency', 'other'])],
            'relationship_other' => ['nullable', 'string', 'max:255', 'required_if:relationship,other'],
            'potential_person_information' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];

        if ($this->input('type') === 'caregiver') {
            $rules['availability'] = ['required', Rule::in(['morning', 'evening', 'other'])];
            $rules['availability_other'] = ['nullable', 'string', 'max:255', 'required_if:availability,other'];
        } else {
            $rules['availability'] = ['nullable'];
            $rules['availability_other'] = ['nullable'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The referral type is required.',
            'type.in' => 'The referral type must be either "client" or "caregiver".',

            'potential_person_name.required' => 'The name of the referred person is required.',
            'potential_person_name.max' => 'The name may not be greater than 255 characters.',

            'potential_person_phone.required' => 'The phone number of the referred person is required.',
            'potential_person_phone.max' => 'The phone number may not be greater than 20 characters.',

            'relationship.required' => 'The relationship with the referred person is required.',
            'relationship.in' => 'Relationship must be one of: family member, friend, agency, or other.',
            'relationship_other.required_if' => 'Please specify the relationship if you selected "other".',

            'availability.required' => 'The availability of the caregiver is required.',
            'availability.in' => 'Availability must be one of: morning, evening, or other.',
            'availability_other.required_if' => 'Please specify the availability if you selected "other".',

            'user_id.exists' => 'The selected user is invalid.',
        ];
    }
}
