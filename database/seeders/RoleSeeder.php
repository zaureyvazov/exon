<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Sistemin tam idarəetmə hüququ',
            ],
            [
                'name' => 'doctor',
                'display_name' => 'Doktor',
                'description' => 'Xəstə qeydiyyatı və göndəriş yaratma hüququ',
            ],
            [
                'name' => 'registrar',
                'display_name' => 'Qeydiyyatçı',
                'description' => 'Göndərişləri görüntüləmə və idarə etmə hüququ',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
