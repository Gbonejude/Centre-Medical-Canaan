<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPTIONIST',
            'DOCTOR',
            'PATIENT',
        ];

        // Supprimer les anciennes permissions qui ne sont plus utilisées
        Permission::whereNotIn('name', $permissions)->delete();

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'is_customer' => $permission === 'PATIENT',
                ]
            );
        }
    }
}
