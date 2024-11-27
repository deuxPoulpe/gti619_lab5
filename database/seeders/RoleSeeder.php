<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Vérifier et créer les rôles si nécessaire
        $adminRole = Role::firstOrCreate(['name' => 'Administrateur']);
        $residentialRole = Role::firstOrCreate(['name' => 'Préposé aux clients résidentiels']);
        $businessRole = Role::firstOrCreate(['name' => 'Préposé aux clients d’affaire']);

        // Créer les utilisateurs et leur assigner les rôles
        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrateur',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);

        $user1 = User::firstOrCreate([
            'email' => 'user1@example.com',
        ], [
            'name' => 'Utilisateur1',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($residentialRole);

        $user2 = User::firstOrCreate([
            'email' => 'user2@example.com',
        ], [
            'name' => 'Utilisateur2',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($businessRole);
    }
}