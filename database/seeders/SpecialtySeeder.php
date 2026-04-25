<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
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
            ['name' => 'Anesthésiologie', 'slug' => 'anesthesiologie', 'description' => 'Anesthésie et réanimation']
        ];
        
        foreach ($specialtiesList as $spec) {
            Specialty::firstOrCreate(
                ['slug' => $spec['slug']],
                ['name' => $spec['name'], 'description' => $spec['description'], 'is_active' => true]
            );
        }
    }
}
