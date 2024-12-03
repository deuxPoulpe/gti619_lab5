<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordComplexity implements Rule
{
    public function passes($attribute, $value)
    {
        // Récupérer les règles de complexité du mot de passe depuis la configuration
        $complexity = config('security.password_complexity');

        // Vérifier la longueur minimale
        if (strlen($value) < $complexity['min_length']) {
            return false;
        }

        // Vérifier si le mot de passe contient des minuscules (si la configuration le demande)
        if ($complexity['lowercase'] && !preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Vérifier si le mot de passe contient des majuscules (si la configuration le demande)
        if ($complexity['uppercase'] && !preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Vérifier si le mot de passe contient des chiffres (si la configuration le demande)
        if ($complexity['numbers'] && !preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Vérifier si le mot de passe contient des caractères spéciaux (si la configuration le demande)
        if ($complexity['special_chars'] && !preg_match('/[@$!%*?&]/', $value)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        // Récupérer les règles de complexité du mot de passe
        $complexity = config('security.password_complexity');

        // Construire le message d'erreur en fonction des options de complexité
        $message = 'Le mot de passe doit comporter au moins ' . $complexity['min_length'] . ' caractères';

        if ($complexity['lowercase']) {
            $message .= ', inclure des minuscules';
        }

        if ($complexity['uppercase']) {
            $message .= ', inclure des majuscules';
        }

        if ($complexity['numbers']) {
            $message .= ', inclure des chiffres';
        }

        if ($complexity['special_chars']) {
            $message .= ', inclure des caractères spéciaux';
        }

        // Retourner le message complet
        return $message . '.';
    }
}
