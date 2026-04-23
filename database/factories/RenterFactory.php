<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Renter>
 */
class RenterFactory extends Factory
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
            'gender' => fake()->randomElement(
                array: array_column(
                    array: Gender::cases(),
                    column_key: 'value',
                ),
            ),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
