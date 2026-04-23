<?php

namespace App\Http\Requests\Messaging\Conversation;

use App\Enums\ConversationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isDraft = $this->input('status') === 'draft';

        return [
            'title' => $isDraft
                ? ['nullable', 'string', 'max:255', 'min:3']
                : ['required', 'string', 'max:255', 'min:3'],

            'content' => ['nullable', 'string', 'max:50000'],

            'user_id' => ['nullable', 'exists:users,id'],

            'status' => ['required', Rule::enum(ConversationStatus::class)],

            'active' => 'nullable|boolean',

            'media' => ['nullable', 'array', 'max:10'],
            'media.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            ],

            'mentions' => ['nullable', 'array', 'max:20'],
            'mentions.*' => [
                function ($attribute, $value, $fail) {
                    if (in_array($value, ['everyone', 'all'])) {
                        return;
                    }

                    if (! is_numeric($value) || ! is_int((int) $value)) {
                        $fail('Each mention must be a valid user ID.');

                        return;
                    }

                    if (! \App\Models\User::where('id', $value)->exists()) {
                        $fail('One of the mentioned users does not exist.');
                    }
                },
                'distinct',
            ],

            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id', 'distinct'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The conversation title is required for published conversations.',
            'title.string' => 'The conversation title must be a valid string.',
            'title.max' => 'The conversation title must not exceed 255 characters.',
            'title.min' => 'The conversation title must be at least 3 characters.',

            'content.string' => 'The content must be a valid string.',
            'content.max' => 'The content must not exceed 50,000 characters.',

            'user_id.exists' => 'The selected user does not exist.',

            'media.array' => 'Media files must be provided as an array.',
            'media.max' => 'You cannot upload more than 10 files.',
            'media.*.file' => 'Each media item must be a valid file.',
            'media.*.max' => 'Each file must not exceed 10MB.',
            'media.*.mimes' => 'The file type is not allowed. Allowed types: jpg, jpeg, png, gif, webp, pdf, doc, docx, xls, xlsx, ppt, pptx, txt.',

            'mentions.array' => 'Mentions must be provided as an array.',
            'mentions.max' => 'You cannot mention more than 20 users.',
            'mentions.*.distinct' => 'You cannot mention the same user multiple times.',

            'permissions.array' => 'Permissions must be provided as an array.',
            'permissions.*.exists' => 'One of the selected permissions does not exist.',
            'permissions.*.distinct' => 'You cannot select the same permission multiple times.',
        ];
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'conversation title',
            'content' => 'conversation content',
            'user_id' => 'user',
            'status' => 'conversation status',
            'media' => 'media files',
            'mentions' => 'mentioned users',
            'permissions' => 'permissions',
        ];
    }
}
