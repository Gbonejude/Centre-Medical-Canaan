<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relationship = $this->faker->randomElement(['family_member', 'friend', 'agency', 'other']);
        $availability = $this->faker->randomElement(['morning', 'evening', 'other']);

        return [
            'type' => $this->faker->randomElement(['client', 'caregiver']),
            'potential_person_name' => $this->faker->name(),
            'potential_person_address' => $this->faker->address(),
            'potential_person_phone' => $this->faker->phoneNumber(),
            'relationship' => $relationship,
            'relationship_other' => $relationship === 'other' ? $this->faker->word() : null,
            'availability' => $availability,
            'availability_other' => $availability === 'other' ? $this->faker->word() : null,
            'potential_person_information' => $this->faker->paragraph(),
            'user_id' => User::factory(),
        ];
    }
}
