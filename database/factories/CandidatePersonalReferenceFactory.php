<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidatePersonalReference>
 */
class CandidatePersonalReferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'occupation' => $this->faker->jobTitle,
        ];
    }
}
