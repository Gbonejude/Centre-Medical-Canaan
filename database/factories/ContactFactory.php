<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    public function configure(): ContactFactory|Factory
    {
        return $this->afterCreating(callback: function (Contact $contact): void {
            $image = UploadedFile::fake()->image('contact.jpg', 600, 600);

            $contact->addMedia($image)
                ->toMediaCollection('Contacts');
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'position' => fake()->jobTitle(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
