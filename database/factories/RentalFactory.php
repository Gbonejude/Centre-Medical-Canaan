<?php

namespace Database\Factories;

use App\Models\Renter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'renter_id' => Renter::factory(),
            'property_address' => $this->faker->address,
            'monthly_rent' => fake()->randomFloat(
                nbMaxDecimals: 2,
                min: 10,
                max: 100,
            ),

            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
        ];
    }
}
