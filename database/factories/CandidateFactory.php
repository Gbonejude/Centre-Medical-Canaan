<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lastname' => fake()->lastName(),
            'firstname' => fake()->firstName(),
            'phone' => fake()->phoneNumber(),
            'birthday' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),

            'gender' => fake()->randomElement(
                array: array_column(
                    array: Gender::cases(),
                    column_key: 'value',
                ),
            ),
            'address' => fake()->address(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
        ];
    }
}
