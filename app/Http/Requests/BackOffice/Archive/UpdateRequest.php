<?php

namespace App\Http\Requests\BackOffice\Archive;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'user_id'          => 'nullable|exists:users,id',
            'permission_ids'   => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'files'            => 'nullable|array|max:5',
            'files.*'          => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx,txt',
            'remove_media'     => 'nullable|array',
            'remove_media.*'   => 'integer|exists:media,id',
        ];
    }
}
