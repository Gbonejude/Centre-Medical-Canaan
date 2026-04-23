<?php

namespace Database\Factories;

use App\Enums\CustomerType;
use App\Enums\Gender;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
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
            'address' => fake()->address(),
            'birthday' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'end_date' => fake()->optional(0.3)->dateTimeBetween('now', '+2 years'),
            'gender' => fake()->randomElement(
                array: array_column(
                    array: Gender::cases(),
                    column_key: 'value',
                ),
            ),
            'type' => fake()->randomElement(
                array: array_column(
                    array: CustomerType::cases(),
                    column_key: 'value',
                ),
            ),
            'email' => fake()->unique()->safeEmail(),
            'user_id' => fake()->optional(0.7)->randomElement(User::pluck('id')->toArray()),
        ];
    }
}
