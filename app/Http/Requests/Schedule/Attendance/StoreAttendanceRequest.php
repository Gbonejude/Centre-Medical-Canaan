<?php

namespace App\Http\Requests\Schedule\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
            'is_present' => 'required|boolean',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The user field is required.',
            'user_id.exists' => 'The selected user is invalid.',
            'schedule_id.required' => 'The schedule field is required.',
            'schedule_id.exists' => 'The selected schedule is invalid.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date must be a valid date.',
            'is_present.required' => 'The presence status field is required.',
            'is_present.boolean' => 'The presence status must be true or false.',
            'check_in_time.date_format' => 'The check-in time must be in HH:MM format.',
            'check_out_time.date_format' => 'The check-out time must be in HH:MM format.',
            'check_out_time.after' => 'The check-out time must be after the check-in time.',
            'notes.max' => 'The notes may not be greater than 1000 characters.',
        ];
    }
}
