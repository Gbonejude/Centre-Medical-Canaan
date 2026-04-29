<?php

namespace Database\Factories;

use App\Models\MedicalService;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalServiceFactory extends Factory
{
    protected $model = MedicalService::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Consultation Générale', 'Cardiologie', 'Pédiatrie', 'Dermatologie',
                'Ophtalmologie', 'Gynécologie', 'Neurologie', 'Radiologie',
                'Analyses Médicales', 'Dentisterie', 'Kinésithérapie', 'Urgences',
                'Oncologie', 'Orthopédie', 'Pneumologie', 'Psychiatrie',
            ]).' '.$this->faker->unique()->word,
            'description' => $this->faker->paragraph,
            'consultation_fee' => $this->faker->randomElement([5000, 10000, 15000, 25000, 50000]),
            'is_active' => true,
        ];
    }
}
