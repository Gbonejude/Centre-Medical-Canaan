<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IndividualMonthlyUpdate>
 */
class IndividualMonthlyUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasConditionChange = $this->faker->boolean(30);
        $hasNewBehavior = $this->faker->boolean(20);
        $hasSpecificReport = $this->faker->boolean(25);

        return [
            'customer_id' => \App\Models\Customer::factory(),
            'year' => $this->faker->numberBetween(2023, 2025),
            'month' => $this->faker->numberBetween(1, 12),
            'overall_health_information' => $this->faker->paragraph(3),
            'appointments' => $this->faker->paragraph(2),
            'social_activities' => $this->faker->paragraph(2),
            'condition_medication_change' => $hasConditionChange,
            'condition_medication_change_description' => $hasConditionChange ? $this->faker->paragraph() : null,
            'new_behavior' => $hasNewBehavior,
            'new_behavior_description' => $hasNewBehavior ? $this->faker->paragraph() : null,
            'specific_report' => $hasSpecificReport,
            'specific_report_description' => $hasSpecificReport ? $this->faker->paragraph() : null,
            'created_by_user_id' => \App\Models\User::factory(),
        ];
    }
}
