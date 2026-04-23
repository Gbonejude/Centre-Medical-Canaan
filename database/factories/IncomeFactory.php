<?php

namespace Database\Factories;

use App\Models\IncomeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Income>
 */
class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(),
            'date' => $this->faker->date(),

            'income_type_id' => IncomeType::factory(),
            'amount_due' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 15,
                max: 100,
            ),
            'amount_paid' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 15,
                max: 100,
            ),
        ];
    }
}
