<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DefaultUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            [
                'email' => env('APP_USER_EMAIL'),
            ],
            [
                'lastname' => 'John',
                'firstname' => 'Doe',
                'gender' => 'male',
                'phone' => '+22890436678',
                'email_verified_at' => now(),
                'password' => Hash::make(env('APP_USER_PASSWORD')),
                'remember_token' => Str::random(10),
            ]
        );

        $users = [
            [
                'email' => 'judasgbone@gmail.com',
                'firstname' => 'Jude',
                'lastname' => 'Gbone',
                'phone' => '+22890510465',
                'gender' => 'male',
                'password' => 'Password2025',
            ],
            [
                'email' => 'ahadjimathieu@gmail.com',
                'firstname' => 'Mathieu',
                'lastname' => 'Ahadji',
                'phone' => '+22898316753',
                'gender' => 'male',
                'password' => 'Password2025',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'firstname' => $userData['firstname'],
                    'lastname' => $userData['lastname'],
                    'phone' => $userData['phone'],
                    'gender' => $userData['gender'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($userData['password']),
                    'remember_token' => Str::random(10),
                ]
            );
            $allPermissions = Permission::all();
            $user->syncPermissions($allPermissions);
        }
        $staffPermissions = Permission::where('is_customer', false)
            ->where('name', '!=', 'SUPER ADMIN')
            ->get();
        $admin->givePermissionTo($staffPermissions);

    }
}
