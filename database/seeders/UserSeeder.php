<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un Directeur
        $directeur = User::create([
            'name' => 'Directeur',
            'email' => 'directeur@example.com',
            'password' => bcrypt('password'),
        ]);
        $directeur->assignRole('Directeur');

        // Créer un Gestionnaire
        $gestionnaire = User::create([
            'name' => 'Gestionnaire',
            'email' => 'gestionnaire@example.com',
            'password' => bcrypt('password'),
        ]);
        $gestionnaire->assignRole('Gestionnaire');

        // Créer un Employé
        $employe = User::create([
            'name' => 'Employé',
            'email' => 'employe@example.com',
            'password' => bcrypt('password'),
        ]);
        $employe->assignRole('Employé');
    }
}
