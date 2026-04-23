<?php

namespace Database\Factories;

use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'expense_type_id' => ExpenseType::factory(),
            'amount' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 15,
                max: 100,
            ),
        ];
    }
}
