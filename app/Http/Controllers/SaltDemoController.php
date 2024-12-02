<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SaltDemoController extends Controller
{
    public function showForm()
    {
        return view('saltdemo');
    }

    public function testPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Si le mot de passe est correct, on affiche le salt
            $salt = substr($user->password, 0, 29);
            return view('saltdemo', compact('salt'));
        }

        return back()->withErrors(['email' => 'Email ou mot de passe incorrect.']);
    }
}
