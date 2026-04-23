<?php

namespace Database\Factories;

use App\Enums\JobOfferResponseStatus;
use App\Models\Guest;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_offer_id' => JobOffer::factory(),
            'guest_id' => Guest::factory(),
            'resume_path' => 'resumes/'.$this->faker->uuid.'.pdf',
            'applied_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'cover_letter_path' => 'cover_letters/'.$this->faker->uuid.'.pdf',
            'status' => fake()->randomElement(
                array: array_column(
                    array: JobOfferResponseStatus::cases(),
                    column_key: 'value',
                ),
            ),

            'message' => $this->faker->sentence(),
        ];
    }
}
