<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Call>
 */
class CallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'call_time' => $this->faker->dateTime(),
            'call_id' => $this->faker->bothify('CALL-####'),
            'title' => $this->faker->sentence(6),
            'respondent_name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'staff_comment' => $this->faker->optional()->sentence(),
            'staff_initial' => strtoupper($this->faker->lexify('??')),
        ];
    }
}
