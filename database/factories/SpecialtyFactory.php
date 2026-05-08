<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Cardiologie', 'Pédiatrie', 'Gynécologie', 'Dermatologie',
            'Ophtalmologie', 'Neurologie', 'Psychiatrie', 'Radiologie',
            'Urologie', 'ORL', 'Stomatologie', 'Chirurgie Générale',
            'Médecine Interne', 'Gastro-entérologie', 'Néphrologie',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'is_active' => true,
        ];
    }
}
