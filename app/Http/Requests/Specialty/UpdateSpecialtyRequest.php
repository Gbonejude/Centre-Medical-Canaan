<?php

namespace App\Http\Requests\Specialty;

use App\Models\Specialty;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialtyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $specialtyUuid = $this->route('specialty');
        $specialty = Specialty::where('uuid', $specialtyUuid)->first();
        $id = $specialty ? $specialty->id : null;

        return [
            'name' => 'required|string|max:255|unique:specialties,name,'.$id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
