<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:COMPLETED,CANCELLED,POSTPONED,PENDING,CONFIRMED',
            'notes' => 'required_if:status,CANCELLED,POSTPONED|nullable|string',
            'appointment_date' => 'required_if:status,POSTPONED|nullable|date|after_or_equal:today',
            'appointment_time' => 'required_if:status,POSTPONED|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required_if' => 'Veuillez indiquer le motif de l\'annulation ou du report.',
            'appointment_date.required_if' => 'Veuillez choisir une nouvelle date pour le report.',
            'appointment_time.required_if' => 'Veuillez choisir une nouvelle heure pour le report.',
        ];
    }
}
