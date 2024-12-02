<?php

// app/Services/SecurityLogger.php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SecurityLogger
{

    public function logSuccessfulLogin($email, $ip, $userAgent)
    {
        Log::info('Connexion réussie pour l\'utilisateur : ' . $email, [
            'user_email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
    }


    public function logFailedLogin($email, $ip, $userAgent)
    {
        Log::warning('Tentative de connexion échouée pour l\'utilisateur : ' . $email, [
            'user_email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
    }


    public function logPasswordChange($email, $ip, $userAgent)
    {
        Log::info('Mot de passe modifié pour l\'utilisateur : ' . $email, [
            'user_email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
    }


    public function logSecurityError($message, array $context = [])
    {
        Log::error($message, $context);
    }
}
