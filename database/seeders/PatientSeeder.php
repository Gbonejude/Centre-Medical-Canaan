<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
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

        // Exact same random seed as DoctorSeeder
        mt_srand(12345);
        shuffle($combinations);
        mt_srand();

        // Take the next 10 combinations to guarantee no overlap with the 20 doctors
        $patientNames = array_slice($combinations, 20, 10);

        foreach ($patientNames as $idx => $name) {
            $first = $name['first'];
            $last = $name['last'];

            $user = User::create([
                'firstname' => $first,
                'lastname' => $last,
                'email' => strtolower($first.'.'.$last.'@patient.com'),
                'phone' => '+337000020'.str_pad($idx, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'gender' => $name['gender'],
                'active' => true,
            ]);
            $user->givePermissionTo('PATIENT');

            Patient::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
