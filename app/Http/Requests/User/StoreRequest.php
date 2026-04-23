<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

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
            'lastname'  => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'phone'     => ['required', 'string', 'unique:users,phone'],
            'gender'    => ['required', 'string'],
            'active'    => ['nullable', 'boolean'],
            'birthday'  => ['nullable', 'date'],
            'image'     => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg', 'max:2048'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ]);
        }
    }
}
