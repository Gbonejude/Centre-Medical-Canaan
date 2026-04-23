<?php

namespace App\Http\Requests\Messaging\Conversation;

use App\Enums\ConversationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:3'],
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
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['exists:media,id'],
            'mentions' => ['nullable', 'array', 'max:20'],
            'mentions.*' => ['exists:users,id', 'distinct'],
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
            'title.required' => 'The conversation title is required.',
            'title.string' => 'The conversation title must be a valid string.',
            'title.max' => 'The conversation title must not exceed 255 characters.',
            'title.min' => 'The conversation title must be at least 3 characters.',

            'content.string' => 'The content must be a valid string.',
            'content.max' => 'The content must not exceed 50,000 characters.',

            'user_id.exists' => 'The selected user does not exist.',

            'status.required' => 'The conversation status is required.',

            'media.array' => 'Media files must be provided as an array.',
            'media.max' => 'You cannot upload more than 10 files.',
            'media.*.file' => 'Each media item must be a valid file.',
            'media.*.max' => 'Each file must not exceed 10MB.',
            'media.*.mimes' => 'The file type is not allowed. Allowed types: jpg, jpeg, png, gif, webp, pdf, doc, docx, xls, xlsx, ppt, pptx, txt.',

            'remove_media.array' => 'Media to remove must be provided as an array.',
            'remove_media.*.exists' => 'One of the media files to remove does not exist.',

            'mentions.array' => 'Mentions must be provided as an array.',
            'mentions.max' => 'You cannot mention more than 20 users.',
            'mentions.*.exists' => 'One of the mentioned users does not exist.',
            'mentions.*.distinct' => 'You cannot mention the same user multiple times.',
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
            'visibility' => 'visibility setting',
            'status' => 'conversation status',
            'tags' => 'tags',
            'media' => 'media files',
            'remove_media' => 'media files to remove',
            'mentions' => 'mentioned users',
        ];
    }
}
