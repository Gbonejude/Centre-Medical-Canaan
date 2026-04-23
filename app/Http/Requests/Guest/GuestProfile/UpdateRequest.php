<?php

declare(strict_types=1);

namespace App\Http\Requests\Guest\GuestProfile;

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
        $guestId = auth('guest')->id();

        return [
            'lastname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:guests,email,'.$guestId],
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|unique:guests,phone,'.$guestId,
        ];
    }
}
