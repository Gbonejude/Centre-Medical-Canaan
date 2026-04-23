<?php

namespace Database\Factories;

use App\Enums\JobOfferStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobOffer>
 */
class JobOfferFactory extends Factory
{
    protected static int $counter = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'OFFER-'.str_pad(self::$counter++, 4, '0', STR_PAD_LEFT),
            'status' => fake()->randomElement(
                array: array_column(
                    array: JobOfferStatus::cases(),
                    column_key: 'value',
                ),
            ),
            'city_zip' => $this->faker->city().' - '.$this->faker->postcode().' ('.$this->faker->randomDigitNotNull().')',
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->sentence(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
