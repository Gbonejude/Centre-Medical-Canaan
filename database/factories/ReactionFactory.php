<?php

namespace Database\Factories;

use App\Enums\ReactionType;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reaction>
 */
class ReactionFactory extends Factory
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
            'reactable_type' => fake()->randomElement([
                'App\\Models\\Conversation',
                'App\\Models\\Comment',
            ]),
            'reactable_id' => function (array $attributes) {

                return match ($attributes['reactable_type']) {
                    'App\\Models\\Conversation' => Conversation::factory()->create()->id,
                    'App\\Models\\Comment' => Comment::factory()->create()->id,
                    default => 1,
                };
            },
            'type' => fake()->randomElement(ReactionType::cases())->value,
        ];
    }
}
