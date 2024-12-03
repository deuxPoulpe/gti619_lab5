<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordHistory;
use App\Services\SecurityLogger;

class PasswordChangeController extends Controller
{

    protected $securityLogger;
    public function __construct(SecurityLogger $securityLogger)
    {
        $this->securityLogger = $securityLogger;
    }

    public function showChangePasswordForm()
    {
        return view('auth.password-change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|confirmed|password_complexity',
        ]);

        $user = auth()->user();
        $passwordHistory = PasswordHistory::where('user_id', $user->id)
                                        ->latest()
                                        ->take(config('security.password_history'))
                                        ->get();

        foreach ($passwordHistory as $history) {
            if (Hash::check($request->password, $history->password)) {
                return back()->withErrors(['password' => 'Le mot de passe ne doit pas être réutilisé parmi les derniers ' . config('security.password_history') . ' mots de passe.']);
            }
        }

        // Vérifier si le mot de passe actuel est correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Mettre à jour le mot de passe
        $user->password = Hash::make($request->password);
        $user->save();
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => Hash::make($request->password),
        ]);
        

        $this->securityLogger->logPasswordChange($user->email, $request->ip(), $request->userAgent());

        return redirect()->route('dashboard')->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}
