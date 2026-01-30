<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        $registrarRole = Role::where('name', 'registrar')->first();

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'surname' => 'User',
            'username' => 'admin',
            'email' => 'admin@exon.com',
            'phone' => '+994501234567',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create Doctor User
        User::create([
            'name' => 'Əli',
            'surname' => 'Məmmədov',
            'username' => 'doctor',
            'email' => 'doctor@exon.com',
            'phone' => '+994501234568',
            'password' => Hash::make('password'),
            'role_id' => $doctorRole->id,
        ]);

        // Create Registrar User
        User::create([
            'name' => 'Ayşə',
            'surname' => 'Həsənova',
            'username' => 'registrar',
            'email' => 'registrar@exon.com',
            'phone' => '+994501234569',
            'password' => Hash::make('password'),
            'role_id' => $registrarRole->id,
        ]);
    }
}
