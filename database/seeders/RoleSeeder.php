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
        $this->generateFixedGridCard($admin, [
            'A1' => 'X7D', 'A2' => 'K2L', 'A3' => 'P3F', 'A4' => 'Q9N',
            'B1' => 'M4P', 'B2' => 'W6T', 'B3' => 'R8Z', 'B4' => 'J5V',
            'C1' => 'T7M', 'C2' => 'Q2X', 'C3' => 'L9K', 'C4' => 'Z4N',
        ]);

        $user1 = User::firstOrCreate([
            'email' => 'user1@example.com',
        ], [
            'name' => 'Utilisateur1',
            'password' => bcrypt('password'),
        ]);
        $user1->assignRole($residentialRole);
        $this->generateFixedGridCard($user1, [
            'A1' => 'A1V', 'A2' => 'B2L', 'A3' => 'C3F', 'A4' => 'D9N',
            'B1' => 'E4P', 'B2' => 'F6T', 'B3' => 'G8Z', 'B4' => 'H5V',
            'C1' => 'I7M', 'C2' => 'J2X', 'C3' => 'K9K', 'C4' => 'L4N',
        ]);

        $user2 = User::firstOrCreate([
            'email' => 'user2@example.com',
        ], [
            'name' => 'Utilisateur2',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($businessRole);
        $this->generateFixedGridCard($user2, [
            'A1' => 'M1V', 'A2' => 'N2L', 'A3' => 'O3F', 'A4' => 'P9N',
            'B1' => 'Q4P', 'B2' => 'R6T', 'B3' => 'S8Z', 'B4' => 'T5V',
            'C1' => 'U7M', 'C2' => 'V2X', 'C3' => 'W9K', 'C4' => 'X4N',
        ]);
    }

    private function generateFixedGridCard(User $user, array $grid)
    {
        if ($user->gridCard) {
            return;
        }

        $user->gridCard()->create([
            'grid' => json_encode($grid),
        ]);
    }
}
