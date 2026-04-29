<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;

class AssignDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:doctors,id',
            'notes' => 'nullable|string',
            'receptionist_notes' => 'nullable|string',
        ];
    }
}
