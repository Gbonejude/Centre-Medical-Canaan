<?php

namespace Database\Factories;

use App\Enums\JobOfferResponseStatus;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOfferResponse>
 */
class JobOfferResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_offer_id' => JobOffer::factory(),
            'message' => $this->faker->sentence(),
            'applied_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(
                array: array_column(
                    array: JobOfferResponseStatus::cases(),
                    column_key: 'value',
                ),
            ),
        ];
    }
}
