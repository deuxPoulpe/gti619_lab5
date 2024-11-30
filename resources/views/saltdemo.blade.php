@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Tester le mot de passe pour un utilisateur</h2>

        <!-- Formulaire pour tester l'email et mot de passe -->
        <form action="{{ route('saltdemo.test') }}" method="POST">
            @csrf

            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="{{ old('email') }}">
                @error('email')
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Tester le mot de passe</button>
        </form>

        @isset($salt)
            <h3>Salt extrait du mot de passe de l'utilisateur :</h3>
            <pre>{{ $salt }}</pre>
        @endisset
    </div>
@endsection
