<?php

namespace App\Http\Requests\Rate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['required', 'exists:users,id'],
            'period_start'  => ['nullable', 'date'],
            'period_end'    => ['nullable', 'date', 'after_or_equal:period_start'],

            'dep1_alias'    => ['nullable', 'string', 'max:100'],
            'dep1_rate'     => ['nullable', 'numeric', 'min:0'],
            'dep1_hours'    => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $userId   = $this->input('user_id');
                    $dep1Rate = $this->input('dep1_rate');
                    // Only required when user has no schedule AND dep1_rate is actually set
                    if ($userId && ($dep1Rate !== null && $dep1Rate !== '' && (float)$dep1Rate > 0)) {
                        $user = \App\Models\User::find($userId);
                        if ($user && (!$user->can_schedule || $user->can_schedule === false) && ($value === null || $value === '')) {
                            $fail('The DEP 1 hours field is required for employees without schedule.');
                        }
                    }
                },
            ],

            'dep2_alias'    => ['nullable', 'string', 'max:100'],
            'dep2_rate'     => ['nullable', 'numeric', 'min:0'],
            'dep2_hours'    => ['nullable', 'numeric', 'min:0'],

            'daily_rate'    => ['nullable', 'numeric', 'min:0'],
            'daily_days'    => ['nullable', 'numeric', 'min:0'],

            'night_rate'    => ['nullable', 'numeric', 'min:0'],
            'night_nights'  => ['nullable', 'numeric', 'min:0'],

            'use_flat_rate' => ['nullable', 'boolean'],
            'prime'         => ['nullable', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'string'],
        ];
    }
}
