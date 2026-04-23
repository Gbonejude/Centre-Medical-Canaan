<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Specialty;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer des Spécialités (10 spécialités)
        $specialties = Specialty::factory()->count(10)->create();

        // 2. Créer des Services Médicaux (15 services variés)
        $services = MedicalService::factory()->count(15)->create();

        // 3. Créer 50 Médecins
        foreach (range(1, 50) as $index) {
            $user = User::factory()->create([
                'password' => Hash::make('password'),
            ]);
            
            Doctor::factory()->create([
                'user_id' => $user->id,
                'medical_service_id' => $services->random()->id,
                'specialty_id' => $specialties->random()->id,
            ]);
        }

        // 4. Créer 50 Patients
        foreach (range(1, 50) as $index) {
            $user = User::factory()->create([
                'password' => Hash::make('password'),
            ]);

            Patient::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        // 5. Créer 100 Rendez-vous
        $doctors = Doctor::all();
        $patients = Patient::all();

        foreach (range(1, 100) as $index) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            
            Appointment::factory()->create([
                'patient_id' => $patient->user_id,
                'doctor_id' => $doctor->user_id,
                'medical_service_id' => $doctor->medical_service_id,
            ]);
        }
    }
}
