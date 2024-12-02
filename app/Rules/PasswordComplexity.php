<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordComplexity implements Rule
{
    public function passes($attribute, $value)
    {
        // Minimum de 8 caractères
        if (strlen($value) < 8) {
            return false;
        }

        // Contient une minuscule, une majuscule, un chiffre et un caractère spécial
        if (!preg_match('/[a-z]/', $value)) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        if (!preg_match('/[@$!%*?&]/', $value)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'Password must be at least 8 characters long, including uppercase letters, lowercase letters, numbers and special characters.';
    }
}
