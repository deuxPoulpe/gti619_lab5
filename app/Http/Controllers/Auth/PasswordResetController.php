<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Services\SecurityLogger;

class PasswordResetController extends Controller
{

    protected $securityLogger;
    public function __construct(SecurityLogger $securityLogger)
    {
        $this->securityLogger = $securityLogger;
    }
    // Afficher le formulaire de demande de réinitialisation de mot de passe
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Envoyer un lien de réinitialisation du mot de passe
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Envoyer l'email de réinitialisation
        $response = Password::sendResetLink(
            $request->only('email')
        );

        // Vérifier si l'envoi a réussi
        if ($response == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Un e-mail avec le lien de réinitialisation a été envoyé.');
        } else {
            return back()->withErrors(['email' => 'L\'adresse e-mail fournie est invalide.']);
        }
    }

    // Afficher le formulaire pour réinitialiser le mot de passe
    public function showResetForm(Request $request)
    {
        return view('auth.passwords.reset', ['token' => $request->token, 'email' => $request->email]);
    }

    // Réinitialiser le mot de passe
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|confirmed|password_complexity|min:8',
            'token' => 'required',
        ]);

        $credentials = $request->only('email', 'password', 'token');

        // Réinitialiser le mot de passe
        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
            $this->securityLogger->logPasswordChange($user->email, $request->ip(), $request->userAgent());
        });
  

        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
        } else {
            return back()->withErrors(['email' => 'L\'adresse e-mail ou le token sont invalides.']);
        }
    }
}
