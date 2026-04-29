<?php

namespace App\Http\Requests\Doctor;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctor = Doctor::where('uuid', $this->route('doctor'))->first();
        $userId = $doctor ? $doctor->user_id : null;

        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email,'.$userId,
            'service_id' => 'required|exists:medical_services,id',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string',
            'gender' => 'nullable|string',
            'birthday' => 'nullable|date',
            'is_available' => 'boolean',
        ];
    }
}
