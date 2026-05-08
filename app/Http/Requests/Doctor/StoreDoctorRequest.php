<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'service_id' => 'required|exists:medical_services,id',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthday' => 'nullable|date',
        ];
    }
}
