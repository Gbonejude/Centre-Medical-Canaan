<?php

namespace App\Http\Requests\ImportantNote;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
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
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'private' => ['nullable', 'boolean'],
            'document' => [
                'sometimes',
                'nullable',
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
                'max:5120',
            ],

            'images' => ['sometimes', 'nullable', 'array'],
            'images.*' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif,svg',
                'max:2048',
            ],

            'remove_images' => 'sometimes|nullable|array',
            'remove_images.*' => 'integer|exists:media,id',

        ];
    }
}
