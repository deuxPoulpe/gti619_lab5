<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

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

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
