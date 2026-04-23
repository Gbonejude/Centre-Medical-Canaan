<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'medical_service_id' => MedicalService::factory(),
            'specialty_id' => Specialty::factory(),
            'bio' => $this->faker->text(200),
            'is_available' => true,
            'availability' => [
                'monday' => ['enabled' => true, 'slots' => [['start' => '08:00', 'end' => '17:00']]],
                'tuesday' => ['enabled' => true, 'slots' => [['start' => '08:00', 'end' => '17:00']]],
                'wednesday' => ['enabled' => true, 'slots' => [['start' => '08:00', 'end' => '17:00']]],
                'thursday' => ['enabled' => true, 'slots' => [['start' => '08:00', 'end' => '17:00']]],
                'friday' => ['enabled' => true, 'slots' => [['start' => '08:00', 'end' => '17:00']]],
                'saturday' => ['enabled' => false, 'slots' => []],
                'sunday' => ['enabled' => false, 'slots' => []],
            ]
        ];
    }
}
