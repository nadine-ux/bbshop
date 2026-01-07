<?php

// database/seeders/RolesAndPermissionsSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
            'manage stock', 'manage suppliers', 'manage employees', 'view reports',
            'create entry', 'create exit', 'launch inventory',
            'manage requests', 'create product',
            'view own requests', 'create request',"manage commandes"
        ];
        foreach ($perms as $p) { Permission::firstOrCreate(['name' => $p]); }

        $directeur    = Role::firstOrCreate(['name' => 'Directeur']);
        $gestionnaire = Role::firstOrCreate(['name' => 'Gestionnaire']);
        $employe      = Role::firstOrCreate(['name' => 'Employé']);

        $directeur->syncPermissions(Permission::all());

        $gestionnaire->syncPermissions([
            'manage stock','create entry','create exit','create product',
            'manage requests','launch inventory','view reports','manage commandes'
        ]);

        $employe->syncPermissions(['create request','view own requests']);

        // Option: premier user devient Directeur
        if ($u = User::first()) { $u->assignRole('Directeur'); }
    }
}
