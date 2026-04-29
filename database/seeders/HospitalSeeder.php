<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HospitalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer des Spécialités Réelles (20 spécialités)
        $specialtiesList = [
            ['name' => 'Cardiologie', 'slug' => 'cardiologie', 'description' => 'Maladies du cœur et des vaisseaux'],
            ['name' => 'Dermatologie', 'slug' => 'dermatologie', 'description' => 'Maladies de la peau, des ongles et des cheveux'],
            ['name' => 'Pédiatrie', 'slug' => 'pediatrie', 'description' => 'Médecine des enfants et adolescents'],
            ['name' => 'Ophtalmologie', 'slug' => 'ophtalmologie', 'description' => 'Maladies des yeux et de la vision'],
            ['name' => 'Orthopédie', 'slug' => 'orthopedie', 'description' => 'Chirurgie des os et des articulations'],
            ['name' => 'Neurologie', 'slug' => 'neurologie', 'description' => 'Maladies du système nerveux'],
            ['name' => 'Gynécologie', 'slug' => 'gynecologie', 'description' => 'Médecine de la femme et de la reproduction'],
            ['name' => 'Psychiatrie', 'slug' => 'psychiatrie', 'description' => 'Santé mentale et troubles psychiques'],
            ['name' => 'Gastro-entérologie', 'slug' => 'gastro-enterologie', 'description' => 'Maladies de l\'appareil digestif'],
            ['name' => 'Urologie', 'slug' => 'urologie', 'description' => 'Appareil urinaire et génital masculin'],
            ['name' => 'Oto-rhino-laryngologie (ORL)', 'slug' => 'orl', 'description' => 'Maladies des oreilles, du nez et de la gorge'],
            ['name' => 'Pneumologie', 'slug' => 'pneumologie', 'description' => 'Maladies des poumons et voies respiratoires'],
            ['name' => 'Endocrinologie', 'slug' => 'endocrinologie', 'description' => 'Troubles hormonaux et métaboliques'],
            ['name' => 'Rhumatologie', 'slug' => 'rhumatologie', 'description' => 'Maladies des os, articulations et muscles'],
            ['name' => 'Oncologie', 'slug' => 'oncologie', 'description' => 'Diagnostic et traitement des cancers'],
            ['name' => 'Hématologie', 'slug' => 'hematologie', 'description' => 'Maladies du sang'],
            ['name' => 'Néphrologie', 'slug' => 'nephrologie', 'description' => 'Maladies des reins'],
            ['name' => 'Allergologie', 'slug' => 'allergologie', 'description' => 'Diagnostic et traitement des allergies'],
            ['name' => 'Gériatrie', 'slug' => 'geriatrie', 'description' => 'Médecine des personnes âgées'],
            ['name' => 'Anesthésiologie', 'slug' => 'anesthesiologie', 'description' => 'Anesthésie et réanimation'],
        ];

        $specialtyModels = collect();
        foreach ($specialtiesList as $spec) {
            $specialtyModels->push(Specialty::firstOrCreate(
                ['slug' => $spec['slug']],
                ['name' => $spec['name'], 'description' => $spec['description'], 'is_active' => true]
            ));
        }

        // 2. Créer des Services Médicaux Réels (15 services)
        $servicesList = [
            ['name' => 'Urgences', 'fee' => 5000, 'desc' => 'Prise en charge immédiate 24/7'],
            ['name' => 'Consultations Externes', 'fee' => 15000, 'desc' => 'Rendez-vous médicaux avec nos spécialistes'],
            ['name' => 'Chirurgie Générale', 'fee' => 50000, 'desc' => 'Opérations chirurgicales et soins post-opératoires'],
            ['name' => 'Maternité', 'fee' => 30000, 'desc' => 'Suivi de grossesse, accouchement et soins aux nouveau-nés'],
            ['name' => 'Imagerie Médicale', 'fee' => 20000, 'desc' => 'Radiographie, Échographie, Scanner'],
            ['name' => 'Laboratoire d\'Analyses', 'fee' => 10000, 'desc' => 'Prélèvements et analyses biologiques'],
            ['name' => 'Soins Intensifs', 'fee' => 75000, 'desc' => 'Réanimation et surveillance continue'],
            ['name' => 'Centre de Dialyse', 'fee' => 40000, 'desc' => 'Traitement de l\'insuffisance rénale'],
            ['name' => 'Service d\'Oncologie', 'fee' => 25000, 'desc' => 'Chimiothérapie et traitements des cancers'],
            ['name' => 'Unité de Soins Palliatifs', 'fee' => 15000, 'desc' => 'Accompagnement et soins de confort'],
            ['name' => 'Centre de Rééducation', 'fee' => 12000, 'desc' => 'Kinésithérapie et réadaptation fonctionnelle'],
            ['name' => 'Service de Psychiatrie', 'fee' => 15000, 'desc' => 'Hospitalisation et suivi psychiatrique'],
            ['name' => 'Médecine Interne', 'fee' => 15000, 'desc' => 'Diagnostic et traitement des maladies complexes'],
            ['name' => 'Bloc Opératoire', 'fee' => 100000, 'desc' => 'Salles d\'interventions chirurgicales équipées'],
            ['name' => 'Pharmacie Hospitalière', 'fee' => 0, 'desc' => 'Délivrance de traitements spécifiques'],
        ];

        $serviceModels = collect();
        foreach ($servicesList as $srv) {
            $serviceModels->push(MedicalService::firstOrCreate(
                ['name' => $srv['name']],
                ['description' => $srv['desc'], 'consultation_fee' => $srv['fee'], 'is_active' => true]
            ));
        }

        $prenomsHommes = ['Amadou', 'Koffi', 'Mamadou', 'Oumar', 'Kwame', 'Ibrahim', 'Seydou', 'Cheikh', 'Yao', 'Aboubacar', 'Salif', 'Tidiane', 'Idrissa', 'Bakary', 'Ousmane'];
        $prenomsFemmes = ['Awa', 'Fatou', 'Aminata', 'Kadiatou', 'Mariam', 'Aïcha', 'Binta', 'Fanta', 'Nafi', 'Adama', 'Safi', 'Zénab', 'Oumou', 'Ramatoulaye', 'Aïssatou'];
        $noms = ['Traoré', 'Diallo', 'Keita', 'Touré', 'Diarra', 'Coulibaly', 'Camara', 'Cissé', 'Sylla', 'Diop', 'Fall', 'Sow', 'Ndiaye', 'Gueye', 'Ba', 'Kone', 'Ouedraogo', 'Kouassi', 'Mensah', 'Diabaté'];

        // 3. Créer 40 Médecins
        $doctorModels = collect();
        for ($i = 1; $i <= 40; $i++) {
            $isMale = rand(0, 1) === 1;
            $first = $isMale ? $prenomsHommes[array_rand($prenomsHommes)] : $prenomsFemmes[array_rand($prenomsFemmes)];
            $last = $noms[array_rand($noms)];

            $user = User::create([
                'firstname' => $first,
                'lastname' => $last,
                'email' => strtolower($first.'.'.$last.$i.'@canaan.com'),
                'phone' => '+336000010'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'active' => true,
            ]);
            $user->givePermissionTo('DOCTOR');

            $doctorModels->push(Doctor::create([
                'user_id' => $user->id,
                'medical_service_id' => $serviceModels->random()->id,
                'specialty_id' => $specialtyModels->random()->id,
                'bio' => 'Dr. '.$last.' est un expert reconnu avec plus de '.rand(5, 25).' ans d\'expérience.',
                'is_available' => true,
            ]));
        }

        // 4. Créer 60 Patients
        $patientModels = collect();
        for ($i = 1; $i <= 60; $i++) {
            $isMale = rand(0, 1) === 1;
            $first = $isMale ? $prenomsHommes[array_rand($prenomsHommes)] : $prenomsFemmes[array_rand($prenomsFemmes)];
            $last = $noms[array_rand($noms)];

            $user = User::create([
                'firstname' => $first,
                'lastname' => $last,
                'email' => strtolower($first.$last.$i.'@patient.com'),
                'phone' => '+337000020'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'active' => true,
            ]);
            $user->givePermissionTo('PATIENT');

            $patientModels->push(Patient::create([
                'user_id' => $user->id,
            ]));
        }

        // 5. Créer 150 Rendez-vous réalistes
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

        foreach (range(1, 150) as $index) {
            $doctor = $doctorModels->random();
            $patient = $patientModels->random();

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
