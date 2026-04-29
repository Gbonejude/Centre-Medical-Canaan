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
            'Douleurs articulaires.'
        ];

        $doctors = Doctor::all();
        $patients = Patient::all();

        foreach (range(1, 150) as $index) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            
            $statuses = ['PENDING', 'CONFIRMED', 'COMPLETED', 'CANCELLED'];
            
            Appointment::create([
                'patient_id' => $patient->user_id,
                'doctor_id' => $doctor->user_id,
                'medical_service_id' => $doctor->medical_service_id,
                'appointment_date' => now()->addDays(rand(-15, 30))->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:%02d', rand(7, 22), rand(0, 1) === 0 ? '00' : '30'),
                'status' => $statuses[array_rand($statuses)],
                'reason' => $motifs[array_rand($motifs)],
            ]);
        }
    }
}
