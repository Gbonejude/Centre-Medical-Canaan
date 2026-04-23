<?php

namespace Database\Factories;

use App\Enums\ShiftType;
use App\Models\JobOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOfferSchedule>
 */
class JobOfferScheduleFactory extends Factory
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
            'schedule_day' => $this->faker->dayOfWeek,
            'start_time' => $this->faker->time,
            'end_time' => $this->faker->time,
            'shift_type' => $this->faker->randomElement(ShiftType::cases())->value,
        ];
    }
}
