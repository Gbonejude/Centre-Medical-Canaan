<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unpaid>
 */
class UnpaidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'note' => $this->faker->sentence(),
            'due_date' => $this->faker->date(),
            'income_id' => Income::factory(),
            'remaining_amount' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 15,
                max: 100,
            ),
        ];
    }
}
