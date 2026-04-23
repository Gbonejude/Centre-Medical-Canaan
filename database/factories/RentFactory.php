<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rent>
 */
class RentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->year(),
            'amount_paid' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 10,
                max: 100,
            ),

            'payment_date' => $this->faker->optional()->date(),
        ];
    }
}
