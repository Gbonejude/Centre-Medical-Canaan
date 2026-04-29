<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $motifs = [
            'Consultation de routine et suivi régulier.',
            'Douleurs abdominales persistantes.',
            'Maux de tête fréquents depuis plusieurs jours.',
            'Renouvellement d\'ordonnance.',
            'Visite de contrôle post-opératoire.',
            'Bilan de santé annuel.',
            'Symptômes grippaux et forte fièvre.',
            'Douleurs articulaires.',
        ];

        $doctors = Doctor::all();
        $patients = Patient::all();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        // 1. Générer des rendez-vous pour la semaine passée (7 jours)
        for ($i = 0; $i <= 7; $i++) {
            $date = now()->subDays($i);
            $count = rand(5, 12); // Entre 5 et 12 rendez-vous par jour

            for ($j = 0; $j < $count; $j++) {
                $doctor = $doctors->random();
                $patient = $patients->random();

                // Statuts logiques pour le passé
                $statuses = [\App\Enums\AppointmentStatus::COMPLETED, \App\Enums\AppointmentStatus::CANCELLED, \App\Enums\AppointmentStatus::POSTPONED];

                Appointment::create([
                    'patient_id' => $patient->user_id,
                    'doctor_id' => $doctor->user_id,
                    'medical_service_id' => $doctor->medical_service_id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => sprintf('%02d:%02d', rand(8, 17), rand(0, 1) === 0 ? '00' : '30'),
                    'status' => $statuses[array_rand($statuses)],
                    'reason' => $motifs[array_rand($motifs)],
                ]);
            }
        }

        // 2. Générer des rendez-vous futurs (prochains 30 jours)
        foreach (range(1, 100) as $index) {
            $doctor = $doctors->random();
            $patient = $patients->random();

            $statuses = [\App\Enums\AppointmentStatus::PENDING, \App\Enums\AppointmentStatus::CONFIRMED];

            Appointment::create([
                'patient_id' => $patient->user_id,
                'doctor_id' => $doctor->user_id,
                'medical_service_id' => $doctor->medical_service_id,
                'appointment_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:%02d', rand(8, 17), rand(0, 1) === 0 ? '00' : '30'),
                'status' => $statuses[array_rand($statuses)],
                'reason' => $motifs[array_rand($motifs)],
            ]);
        }
    }
}
