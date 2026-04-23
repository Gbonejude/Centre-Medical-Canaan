<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduledMonth = $this->faker->monthName();
        $scheduledYear = $this->faker->year();

        return [
            'instructor_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'instructor_id' => User::factory(),
            'scheduled_month' => $scheduledMonth,
            'scheduled_year' => $scheduledYear,
            'is_unlocked' => $this->faker->boolean(20),
        ];
    }
}
