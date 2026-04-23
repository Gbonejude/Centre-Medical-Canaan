<?php

namespace App\Http\Requests\Assurance;

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
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'fax_number' => ['nullable', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],

            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'client_types' => ['nullable', 'array'],
            'client_types.*' => ['string'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The assurance name is required.',
            'name.string' => 'The assurance name must be a valid string.',
            'name.max' => 'The assurance name must not exceed 255 characters.',

            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already used.',
            'email.max' => 'The email must not exceed 255 characters.',

            'phone.unique' => 'This phone number is already used.',

            'fax_number.unique' => 'This fax number is already used.',

            'address.max' => 'The address must not exceed 255 characters.',
            'active.boolean' => 'The active field must be true or false.',
        ];
    }

    /**
     * Custom attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'name' => 'assurance name',
            'email' => 'email address',
            'phone' => 'phone number',
            'address' => 'physical address',
            'active' => 'status',
        ];
    }
}
