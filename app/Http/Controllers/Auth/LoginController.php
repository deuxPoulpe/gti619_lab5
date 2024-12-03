<?php
namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Services\SecurityLogger;


class LoginController extends Controller
{
    protected $maxAttempts; // Nombre maximum de tentatives
    protected $decaySeconds; // Délai entre chaque tentative

    protected $securityLogger;
    public function __construct(SecurityLogger $securityLogger)
    {
        $this->securityLogger = $securityLogger;
        $this->maxAttempts = config('security.max_attempts');
        $this->decaySeconds = config('security.delay_between_attempts');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Créer une clé de limitation basée sur l'IP et l'email
        $key = $this->throttleKey($request);
        $user = User::where('email', $request->email)->first();

        // Vérifier si l'utilisateur est bloqué
        if ($user && $user->is_blocked) {
            return back()->withErrors([
                'email' => 'Votre compte est bloqué. Contactez l\'administrateur pour le débloquer.',
            ]);
        }

        // Récupérer les tentatives échouées depuis le cache
        $attempts = Cache::get($key, 0);

        // Vérifier si trop de tentatives ont été effectuées
        if ($attempts >= $this->maxAttempts) {
            // Bloquer l'utilisateur après le nombre de tentatives échouées
            if ($user) {
                $user->update(['is_blocked' => true]);
            }
            return back()->withErrors([
                'email' => 'Votre compte est bloqué après plusieurs tentatives échouées. Contactez l\'administrateur pour le débloquer.',
            ]);
        }

        $credentials = $request->only('email', 'password');

        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            $this->securityLogger->logSuccessfulLogin($request->email, $request->ip(), $request->userAgent());
            RateLimiter::clear($key); // Réinitialiser le compteur après une connexion réussie
            $request->session()->regenerate();

            Cache::forget($key); // Réinitialiser les tentatives échouées après une connexion réussie
            $user = Auth::user();
            $gridCard = $user->gridCard;

            if (!$gridCard) {
                Auth::logout();
                return back()->withErrors(['grid_value' => 'No Grid Card found for this user.']);
            }

            $expectedValue = null;
            switch ($user->email) {
                case 'admin@example.com':
                    $expectedValue = 1000;
                    break;
                case 'user1@example.com':
                    $expectedValue = 2000;
                    break;
                case 'user2@example.com':
                    $expectedValue = 3000;
                    break;
                default:
                    Auth::logout();
                    return back()->withErrors(['grid_value' => 'Unexpected user email.']);
            }

            if ($request->input('grid_value') == $expectedValue) {
                return redirect()->intended('dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['grid_value' => 'Invalid Grid Card value.']);
            }

        }

        // Vérifier si l'utilisateur doit attendre avant de réessayer
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $remainingTime = RateLimiter::availableIn($key); // Temps restant avant la prochaine tentative
            return back()->withErrors([
                'email' => "Tentatives échouées. Veuillez réessayer dans $remainingTime secondes.",
            ]);
        }

        // Tentative échouée
        // Augmenter le nombre de tentatives échouées et appliquer un délai entre chaque tentative
        RateLimiter::hit($key, $this->decaySeconds);
        $this->securityLogger->logFailedLogin($request->email, $request->ip(), $request->userAgent());

        // Stocker les tentatives échouées dans le cache
        Cache::put($key, ++$attempts, $this->decaySeconds * 60);

        // Vérifier le nombre de tentatives restantes
        $remainingAttempts = $this->maxAttempts - $attempts;
        $remainingTime = RateLimiter::availableIn($key); // Temps restant avant la prochaine tentative

        // Afficher un message en fonction des tentatives restantes       
        if ($remainingAttempts > 0) {
            return back()->withErrors([
                'email' => "Identifiants invalides. Vous avez encore $remainingAttempts tentatives avant le blocage. Attendez $remainingTime secondes avant de réessayer.",
            ]);
        } else {
            return back()->withErrors([
                'email' => "Identifiants invalides. Vous avez atteint le nombre maximum de tentatives. Votre compte sera bloqué si vous continuez à échouer. ",
            ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}

