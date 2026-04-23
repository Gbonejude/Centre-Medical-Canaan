<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
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
            'lastname'      => ['required', 'string', 'max:255'],
            'firstname'     => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone'         => ['nullable', 'string'],
            'gender'        => ['nullable', 'string'],
            'active'        => ['nullable', 'boolean'],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            'birthday'      => ['nullable', 'date'],
            'image'         => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg', 'max:2048'],
            'remove_image'  => ['nullable', 'boolean'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('remove_image')) {
            $this->merge([
                'remove_image' => filter_var($this->remove_image, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        if ($this->has('active')) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }

        if ($this->has('permissions') && ! is_array($this->permissions)) {
            $this->merge([
                'permissions' => [],
            ]);
        }
    }
}
