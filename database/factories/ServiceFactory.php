<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Residential Service',
                'Day Habilitation',
                'Community Living',
                'Personal Care',
                'Respite Care',
            ]),
            'description' => fake()->optional(0.7)->sentence(),
        ];
    }
}
