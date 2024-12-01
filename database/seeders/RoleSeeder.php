<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\GridCard;

class RoleSeeder extends Seeder
{
    public function run()
    {

        $adminRole = Role::firstOrCreate(['name' => 'Administrateur']);
        $residentialRole = Role::firstOrCreate(['name' => 'Préposé aux clients résidentiels']);
        $businessRole = Role::firstOrCreate(['name' => 'Préposé aux clients d’affaire']);


        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrateur',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole($adminRole);
        $this->generateFixedGridCard($admin, 1000);

        $user1 = User::firstOrCreate([
            'email' => 'user1@example.com',
        ], [
            'name' => 'Utilisateur1',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($residentialRole);
        $this->generateFixedGridCard($user1, 2000);

        $user2 = User::firstOrCreate([
            'email' => 'user2@example.com',
        ], [
            'name' => 'Utilisateur2',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($businessRole);
        $this->generateFixedGridCard($user2, 3000);
    }

    private function generateFixedGridCard(User $user, $fixedValue)
    {

        if ($user->gridCard) {
            return;
        }


        $grid = [];
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $grid[$i][$j] = $fixedValue;
            }
        }


        $user->gridCard()->create([
            'grid' => json_encode($grid),
        ]);
    }
}
