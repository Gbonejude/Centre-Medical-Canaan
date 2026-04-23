<?php

namespace Database\Factories;

use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issueDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $expiryDate = $this->faker->dateTimeBetween('now', '+2 years');
        $reminderDate = $this->faker->dateTimeBetween('now', $expiryDate);

        return [
            'uuid' => Str::uuid(),
            'issue_date' => $this->faker->optional(0.7)->dateTime($issueDate),
            'expiry_date' => $expiryDate,
            'reminder_date' => $this->faker->optional(0.6)->dateTime($reminderDate),
            'notes' => $this->faker->optional(0.5)->paragraph(),
            'document_type_id' => DocumentType::factory(),
            'user_id' => User::factory(),
            'created_by' => User::factory(),
            'updated_by' => $this->faker->optional(0.7)->randomElement([User::factory()]),
        ];
    }
}
