<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private $predefinedGrids = [
        'admin@example.com' => [
            'A3' => 'P3F',
            'B2' => 'W6T',
            'C1' => 'T7M'
        ],
        'user1@example.com' => [
            'A3' => 'C3F',
            'B2' => 'F6T',
            'C1' => 'I7M'
        ],
        'user2@example.com' => [
            'A3' => 'O3F',
            'B2' => 'R6T',
            'C1' => 'U7M'
        ]
    ];

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->withInput();
        }

        $user = Auth::user();
        $userEmail = $user->email;

        // Check if grid exists for this user
        if (!isset($this->predefinedGrids[$userEmail])) {
            Auth::logout();
            return back()->withErrors(['grid_value' => 'Security grid not configured.']);
        }

        $gridValues = $this->predefinedGrids[$userEmail];
        $requiredCoordinates = ['A3', 'B2', 'C1'];

        // Detailed grid coordinate validation
        $errors = [];
        foreach ($requiredCoordinates as $coordinate) {
            $inputValue = $request->input($coordinate);

            if (!$inputValue) {
                $errors[$coordinate] = "Missing $coordinate coordinate";
            } elseif ($inputValue !== $gridValues[$coordinate]) {
                $errors[$coordinate] = "Incorrect $coordinate value";
            }
        }

        if (!empty($errors)) {
            Auth::logout();
            return back()->withErrors($errors)->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
