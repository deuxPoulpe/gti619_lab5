<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class SecuritySettingsController extends Controller
{
    // Affiche les paramètres de sécurité actuels
    public function index()
    {
        // Récupère les paramètres actuels depuis la configuration
        $settings = config('security');
        return view('admin.security-settings', compact('settings'));
    }

    // Met à jour les paramètres de sécurité
    public function update(Request $request)
    {
        // Validation des données envoyées
        $validated = $request->validate([
            'max_attempts' => 'required|integer|min:1',
            'delay_between_attempts' => 'required|integer|min:1',
            'password_expiry' => 'required|integer|min:1',
            'password_complexity' => 'required|array',  // Nous attendons un tableau pour la complexité
            'password_history' => 'required|integer|min:0',
        ]);

        // Validation de la complexité du mot de passe
        $passwordComplexity = $validated['password_complexity'];

        // Assurez-vous que les sous-clés de la complexité sont définies et valides
        $passwordComplexity = [
            'min_length' => isset($passwordComplexity['min_length']) ? (int) $passwordComplexity['min_length'] : 8,
            'uppercase' => isset($passwordComplexity['uppercase']) ? (bool) $passwordComplexity['uppercase'] : false,
            'lowercase' => isset($passwordComplexity['lowercase']) ? (bool) $passwordComplexity['lowercase'] : false,
            'numbers' => isset($passwordComplexity['numbers']) ? (bool) $passwordComplexity['numbers'] : false,
            'special_chars' => isset($passwordComplexity['special_chars']) ? (bool) $passwordComplexity['special_chars'] : false,
        ];

        // Chemin vers le fichier de configuration
        $path = config_path('security.php');

        // Génère un contenu PHP pour le fichier de configuration
        $configContent = "<?php\n\nreturn [\n";
        foreach ($validated as $key => $value) {
            // Vérifie si la valeur est un tableau (comme pour password_complexity)
            if ($key == 'password_complexity') {
                $configContent .= "    '{$key}' => " . var_export($passwordComplexity, true) . ",\n";
            } else {
                $configContent .= "    '{$key}' => " . (is_numeric($value) ? $value : "'{$value}'") . ",\n";
            }
        }
        $configContent .= "];\n";

        // Écrit les nouveaux paramètres dans le fichier de configuration
        File::put($path, $configContent);

        // Recharge la configuration en mémoire pour refléter les changements
        \Artisan::call('config:cache');

        // Redirige l'utilisateur avec un message de succès
        return redirect()->route('admin.security-settings')->with('status', 'Paramètres mis à jour avec succès.');
    }
}
