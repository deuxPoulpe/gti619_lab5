<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ResetPasswordAndUnblock extends Command
{
    protected $signature = 'user:reset-password {email} {password}';
    protected $description = 'Débloquer un utilisateur et réinitialiser son mot de passe';

    public function handle()
    {
        $email = $this->argument('email');
        $newPassword = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('Utilisateur non trouvé.');
            return;
        }

        // Débloquer l'utilisateur et mettre à jour le mot de passe
        $user->update([
            'is_blocked' => false,
            'password' => Hash::make($newPassword),
        ]);

        // Supprimer le cache des tentatives échouées
        $key = Str::lower($email) . '|' . request()->ip();
        Cache::forget($key);

        $this->info('L\'utilisateur a été débloqué, son mot de passe a été réinitialisé et le cache des tentatives échouées a été supprimé.');
    }
}