<?php

namespace App\Http\Requests\Messaging\Comment;

use App\Enums\ReactionType;
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
            'user_id' => ['nullable', 'exists:users,id'],
            'conversation_id' => ['required', 'exists:conversations,id'],
            'parent_id' => ['nullable', 'exists:comments,id'],

            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => [
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt,mp4,avi,mov,wmv,flv,webm,mp3,wav,ogg',
            ],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['exists:media,id'],
            'mentions' => ['nullable', 'array', 'max:10'],
            'mentions.*' => ['exists:users,id', 'distinct'],
            'reaction_type' => ['nullable', Rule::enum(ReactionType::class)],
            'reaction_user_id' => ['nullable', 'exists:users,id'],
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
            'title.required' => 'The comment title is required.',
            'title.string' => 'The comment title must be a valid string.',
            'title.max' => 'The comment title must not exceed 255 characters.',
            'title.min' => 'The comment title must be at least 3 characters.',

            'user_id.exists' => 'The selected user does not exist.',

            'conversation_id.required' => 'The conversation is required.',
            'conversation_id.exists' => 'The selected conversation does not exist.',

            'parent_id.exists' => 'The parent comment does not exist.',

            'media.array' => 'Media files must be provided as an array.',
            'media.max' => 'You cannot upload more than 5 files.',
            'media.*.file' => 'Each media item must be a valid file.',
            'media.*.max' => 'Each file must not exceed 5MB.',
            'media.*.mimes' => 'The file type is not allowed. Allowed types: jpg, jpeg, png, gif, webp, pdf, doc, docx, txt, mp4, avi, mov, wmv, flv, webm, mp3, wav, ogg.',

            'remove_media.array' => 'Media to remove must be provided as an array.',
            'remove_media.*.exists' => 'One of the media files to remove does not exist.',

            'mentions.array' => 'Mentions must be provided as an array.',
            'mentions.max' => 'You cannot mention more than 10 users.',
            'mentions.*.exists' => 'One of the mentioned users does not exist.',
            'mentions.*.distinct' => 'You cannot mention the same user multiple times.',

            'reaction_type.enum' => 'The reaction type must be a valid reaction.',
            'reaction_user_id.exists' => 'The user for the reaction does not exist.',
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
            'title' => 'comment title',
            'user_id' => 'user',
            'conversation_id' => 'conversation',
            'parent_id' => 'parent comment',
            'tags' => 'tags',
            'media' => 'media files',
            'remove_media' => 'media files to remove',
            'mentions' => 'mentioned users',
            'reaction_type' => 'reaction type',
            'reaction_user_id' => 'reaction user',
        ];
    }
}
