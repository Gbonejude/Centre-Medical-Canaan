<?php

namespace App\Http\Requests\Hospital;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreAppointmentRequest extends FormRequest
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
            'medical_service_id' => 'required|exists:medical_services,id',
            'appointment_date'   => 'required|date|after_or_equal:today',
            'appointment_time'   => 'required',
            'reason'             => 'nullable|string',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled(['appointment_date', 'appointment_time'])) {
                try {
                    $appointmentDateTime = Carbon::parse($this->appointment_date . ' ' . $this->appointment_time);
                    if ($appointmentDateTime->isPast()) {
                        $validator->errors()->add(
                            'appointment_time',
                            'L\'heure choisie est déjà passée pour cette date. Veuillez sélectionner une heure future.'
                        );
                    }
                } catch (\Exception $e) {
                    // Carbon will handle invalid formats via rules
                }
            }
        });
    }
}
