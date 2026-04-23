<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The :attribute is required.',
            'email.email' => 'Please enter a valid :attribute.',
            'password.required' => 'The :attribute is required.',
            'password.string' => 'The :attribute must be a string.',
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'email address',
            'password' => 'password',
        ];
    }
}
