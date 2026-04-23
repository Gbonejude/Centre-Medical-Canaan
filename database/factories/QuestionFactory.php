<?php

namespace Database\Factories;

use App\Models\Quizze;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quizze_id' => Quizze::factory(),
            'question_text' => $this->faker->paragraph(),
        ];
    }
}
