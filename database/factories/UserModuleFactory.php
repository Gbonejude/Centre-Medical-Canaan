<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserModule>
 */
class UserModuleFactory extends Factory
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
            'module_id' => Module::factory(),
            'title' => $this->faker->sentence(6),
            'order' => $this->faker->numberBetween(1, 12),
            'content' => $this->faker->paragraph(),
            'description' => $this->faker->paragraph(),

        ];
    }
}
