<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Enums\CustomerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'firstname' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^(\+1\s?)?(\(\d{3}\)|\d{3})[\s\-]?\d{3}[\s\-]?\d{4}$/'],
            'medicaid_id' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string'],
            'type' => ['required', 'string'],
            'care_house_id' => [
                Rule::requiredIf(function () {
                    return !in_array($this->input('type'), [
                        CustomerType::HOME_CARE_CLIENTS->value,
                        CustomerType::DD_WAIVER_CLIENTS->value,
                    ]);
                }),
                'nullable',
                'exists:care_houses,id'
            ],
            'active' => ['nullable', 'boolean'],
            'address' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'assurance_id' => [
                Rule::requiredIf(function () {
                    return in_array(
                        $this->input('type'),
                        [
                            CustomerType::HOME_CARE_CLIENTS->value,
                            CustomerType::RESIDENTIAL_CLIENTS->value,
                        ]
                    );
                }),
                'nullable',
                'exists:assurances,id',
            ],
            'caregiver_ids' => [
                Rule::requiredIf(function () {
                    return in_array($this->input('type'), [
                        CustomerType::DD_WAIVER_CLIENTS->value,
                        CustomerType::IN_HOME_CLIENTS->value,
                    ]);
                }),
                'nullable',
                'array',
            ],
            'caregiver_ids.*' => ['exists:users,id'],
        ];
    }
}
