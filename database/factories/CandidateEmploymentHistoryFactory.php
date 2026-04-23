<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CandidateEmploymentHistory>
 */
class CandidateEmploymentHistoryFactory extends Factory
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
            'from_date' => $this->faker->dateTimeBetween('-10 years', '-2 years')->format('Y-m-d'),
            'to_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'job_title' => $this->faker->jobTitle,
            'employer_name' => $this->faker->company,
            'employer_address' => $this->faker->address,
            'reason_for_leaving' => $this->faker->optional()->sentence,
            'type_of_work_performed' => $this->faker->paragraph,
            'order' => $this->faker->numberBetween(1, 3),
        ];
    }
}
