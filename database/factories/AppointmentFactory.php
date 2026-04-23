<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\MedicalService;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'patient_id' => User::factory(),
            'medical_service_id' => MedicalService::factory(),
            'doctor_id' => User::factory(),
            'appointment_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'appointment_time' => $this->faker->time('H:i'),
            'status' => $this->faker->randomElement(['PENDING', 'CONFIRMED', 'COMPLETED', 'CANCELLED']),
            'reason' => $this->faker->sentence,
            'receptionist_notes' => $this->faker->text(100),
            'confirmed_at' => now(),
        ];
    }
}
