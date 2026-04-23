<?php

namespace App\Http\Requests\Contact;

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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/', 'unique:contacts,phone'],
            'position' => ['required', 'string', 'max:255'],
            'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:contacts,email'],

        ];
    }
}
