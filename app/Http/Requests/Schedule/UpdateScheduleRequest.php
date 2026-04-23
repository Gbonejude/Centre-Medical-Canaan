<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'is_off' => 'required|boolean',
            'shift_type' => 'nullable|in:hourly,daily,night',
            'care_house_id' => 'required_if:is_off,false|nullable|exists:care_houses,id',
            'start_time' => 'required_if:is_off,false|nullable|date_format:H:i',
            // Removed 'after:start_time' validation to allow overnight shifts (e.g., 9PM to 7AM)
            'end_time' => 'required_if:is_off,false|nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'check_in_time' => ['nullable', 'regex:/^\d{1,2}:\d{2}\s*(AM|PM)$/i'],
            'check_out_time' => ['nullable', 'regex:/^\d{1,2}:\d{2}\s*(AM|PM)$/i'],
            'is_present' => 'nullable|boolean',
            'attendance_notes' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->is_off) {
            $this->merge([
                'start_time' => null,
                'end_time' => null,
            ]);
        } else {
            if ($this->start_time) {
                $this->merge([
                    'start_time' => date('H:i', strtotime($this->start_time)),
                ]);
            }

            if ($this->end_time) {
                $this->merge([
                    'end_time' => date('H:i', strtotime($this->end_time)),
                ]);
            }
        }
    }
}
