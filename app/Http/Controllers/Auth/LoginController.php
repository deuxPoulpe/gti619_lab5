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
    private $predefinedGrids = [
        'admin@example.com' => [
            'A1' => 'X7D', 'A2' => 'K2L', 'A3' => 'P3F', 'A4' => 'Q9N',
            'B1' => 'M4P', 'B2' => 'W6T', 'B3' => 'R8Z', 'B4' => 'J5V',
            'C1' => 'T7M', 'C2' => 'Q2X', 'C3' => 'L9K', 'C4' => 'Z4N',
        ],
        'user1@example.com' => [
            'A1' => 'A1V', 'A2' => 'B2L', 'A3' => 'C3F', 'A4' => 'D9N',
            'B1' => 'E4P', 'B2' => 'F6T', 'B3' => 'G8Z', 'B4' => 'H5V',
            'C1' => 'I7M', 'C2' => 'J2X', 'C3' => 'K9K', 'C4' => 'L4N',
        ],
        'user2@example.com' => [
            'A1' => 'M1V', 'A2' => 'N2L', 'A3' => 'O3F', 'A4' => 'P9N',
            'B1' => 'Q4P', 'B2' => 'R6T', 'B3' => 'S8Z', 'B4' => 'T5V',
            'C1' => 'U7M', 'C2' => 'V2X', 'C3' => 'W9K', 'C4' => 'X4N',
        ],
    ];
    protected $maxAttempts = 5; // Nombre maximal de tentatives
    protected $decaySeconds = 10; // Durée de blocage entre les tentatives

    protected $securityLogger;
    public function __construct(SecurityLogger $securityLogger)
    {
        $this->securityLogger = $securityLogger;
    }


    public function showLoginForm()
    {
        $allCoordinates = array_keys($this->predefinedGrids['admin@example.com']);
        $requiredCoordinates = collect($allCoordinates)->random(3)->toArray();

          session(['required_coordinates' => $requiredCoordinates]);

        return view('auth.login', ['required_coordinates' => $requiredCoordinates]);
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

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->withInput();
        }

        $user = Auth::user();
        $userEmail = $user->email;


        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            $this->securityLogger->logSuccessfulLogin($request->email, $request->ip(), $request->userAgent());
            RateLimiter::clear($key); // Réinitialiser le compteur après une connexion réussie
            $request->session()->regenerate();

            Cache::forget($key); // Réinitialiser les tentatives échouées après une connexion réussie
            $user = Auth::user();
            $gridCard = $user->gridCard;

        if (!isset($this->predefinedGrids[$userEmail])) {
            Auth::logout();
            return back()->withErrors(['grid_value' => 'Security grid not configured.']);
        }

        $gridValues = $this->predefinedGrids[$userEmail];
        $requiredCoordinates = session('required_coordinates', []);


        $errors = [];
        foreach ($requiredCoordinates as $coordinate) {
            $inputValue = $request->input($coordinate);

            if (!$inputValue) {
                $errors[$coordinate] = "Missing $coordinate coordinate.";
            } elseif ($inputValue !== $gridValues[$coordinate]) {
                $errors[$coordinate] = "Incorrect $coordinate value.";
            }
          
        if (!empty($errors)) {
            Auth::logout();
            return back()->withErrors($errors)->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended('dashboard');

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

