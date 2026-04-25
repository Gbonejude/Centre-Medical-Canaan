<?php

namespace Database\Seeders;

use App\Models\MedicalService;
use Illuminate\Database\Seeder;

class MedicalServiceSeeder extends Seeder
{
    public function run(): void
    {
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
            ['name' => 'Autres', 'fee' => 10000, 'desc' => 'Service générique pour toute demande non répertoriée']
        ];

        foreach ($servicesList as $srv) {
            MedicalService::firstOrCreate(
                ['name' => $srv['name']],
                ['description' => $srv['desc'], 'consultation_fee' => $srv['fee'], 'is_active' => true]
            );
        }
    }
}
