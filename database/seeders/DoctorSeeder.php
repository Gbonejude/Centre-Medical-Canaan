<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\MedicalService;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $prenomsHommes = ['Amadou', 'Koffi', 'Mamadou', 'Oumar', 'Kwame', 'Ibrahim', 'Seydou', 'Cheikh', 'Yao', 'Aboubacar', 'Salif', 'Tidiane', 'Idrissa', 'Bakary', 'Ousmane'];
        $prenomsFemmes = ['Awa', 'Fatou', 'Aminata', 'Kadiatou', 'Mariam', 'Aïcha', 'Binta', 'Fanta', 'Nafi', 'Adama', 'Safi', 'Zénab', 'Oumou', 'Ramatoulaye', 'Aïssatou'];
        $noms = ['Traoré', 'Diallo', 'Keita', 'Touré', 'Diarra', 'Coulibaly', 'Camara', 'Cissé', 'Sylla', 'Diop', 'Fall', 'Sow', 'Ndiaye', 'Gueye', 'Ba', 'Kone', 'Ouedraogo', 'Kouassi', 'Mensah', 'Diabaté'];

        $combinations = [];
        foreach ($prenomsHommes as $first) {
            foreach ($noms as $last) {
                $combinations[] = ['first' => $first, 'last' => $last, 'gender' => 'male'];
            }
        }
        foreach ($prenomsFemmes as $first) {
            foreach ($noms as $last) {
                $combinations[] = ['first' => $first, 'last' => $last, 'gender' => 'female'];
            }
        }
        
        mt_srand(12345);
        shuffle($combinations);
        mt_srand();

        $specialties = Specialty::all();
        $doctorNames = array_slice($combinations, 0, $specialties->count());
        $serviceModels = MedicalService::all()->values();

        foreach ($specialties as $idx => $specialty) {
            $name = $doctorNames[$idx];
            $first = $name['first'];
            $last  = $name['last'];

            // Cyclic: chaque service a au moins un médecin
            $service = $serviceModels[$idx % $serviceModels->count()];

            $user = User::create([
                'firstname' => $first,
                'lastname'  => $last,
                'email'     => strtolower($first.'.'.$last.'@canaan.com'),
                'phone'     => '+336000010' . str_pad($idx, 2, '0', STR_PAD_LEFT),
                'password'  => Hash::make('password'),
                'birthday'  => now()->subYears(rand(30, 60))->subDays(rand(1, 365))->format('Y-m-d'),
                'gender'    => $name['gender'],
                'active'    => true,
            ]);
            $user->givePermissionTo('DOCTOR');

            $doctor = Doctor::create([
                'user_id'            => $user->id,
                'medical_service_id' => $service->id,
                'specialty_id'       => $specialty->id,
                'bio'         => 'Dr. '.$last.' est le spécialiste référent en '.$specialty->name.'.',
                'is_available' => true,
            ]);

            // Pivot spécialités uniquement
            $doctor->specialties()->attach([$specialty->id]);
        }
    }
}
