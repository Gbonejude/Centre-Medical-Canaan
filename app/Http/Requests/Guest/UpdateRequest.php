<?php

declare(strict_types=1);

namespace App\Http\Requests\Guest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $guestId = $this->route('guest')?->id ?? $this->route('id');

        return [
            'lastname' => ['sometimes', 'required', 'string', 'max:255'],
            'firstname' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('guests', 'email')->ignore($guestId),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:8',
                Rule::unique('guests', 'phone')->ignore($guestId),
            ],
            'active' => ['sometimes', 'nullable', 'boolean'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
