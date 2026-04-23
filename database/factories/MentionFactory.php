<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mention>
 */
class MentionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'mentionable_type' => 'App\\Models\\User',
            'mentionable_id' => User::factory()->create()->id,
            'mentionable_in_type' => fake()->randomElement([
                'App\\Models\\Conversation',
                'App\\Models\\Comment',
            ]),
            'mentionable_in_id' => function (array $attributes) {
                return match ($attributes['mentionable_in_type']) {
                    'App\\Models\\Conversation' => Conversation::factory()->create()->id,
                    'App\\Models\\Comment' => Comment::factory()->create()->id,
                    default => 1,
                };
            },
        ];
    }
}
