<?php

namespace App\Http\Requests\DoctorSchedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'availability' => 'required|array',
        ];
    }
}
