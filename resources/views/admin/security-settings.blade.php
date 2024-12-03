@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Paramètres de sécurité</h1>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.security-settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Nombre de tentatives d'authentification -->
        <div class="form-group">
            <label for="max_attempts">Nombre maximal de tentatives</label>
            <input type="number" class="form-control" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', $settings['max_attempts']) }}" required>
        </div>

        <!-- Délai entre chaque tentative échouée -->
        <div class="form-group">
            <label for="delay_between_attempts">Délai entre chaque tentative échouée (en secondes)</label>
            <input type="number" class="form-control" id="delay_between_attempts" name="delay_between_attempts" value="{{ old('delay_between_attempts', $settings['delay_between_attempts']) }}" required>
        </div>

        <!-- Durée de validité du mot de passe -->
        <div class="form-group">
            <label for="password_expiry">Durée de validité du mot de passe (en jours)</label>
            <input type="number" class="form-control" id="password_expiry" name="password_expiry" value="{{ old('password_expiry', $settings['password_expiry']) }}" required>
        </div>

        <!-- Complexité du mot de passe -->
        <h3>Complexité du mot de passe</h3>
        <div class="form-group">
            <label for="password_complexity[min_length]">Longueur minimale</label>
            <input type="number" class="form-control" id="password_complexity[min_length]" name="password_complexity[min_length]" value="{{ old('password_complexity[min_length]', $settings['password_complexity']['min_length'] ?? 8) }}" required>
        </div>
        <div class="form-group">
            <label for="password_complexity[uppercase]">Majuscules requises</label>
            <input type="checkbox" class="form-control" id="password_complexity[uppercase]" name="password_complexity[uppercase]" {{ old('password_complexity[uppercase]', isset($settings['password_complexity']['uppercase']) && $settings['password_complexity']['uppercase'] ? 'checked' : '') }}>
        </div>
        <div class="form-group">
            <label for="password_complexity[lowercase]">Minuscules requises</label>
            <input type="checkbox" class="form-control" id="password_complexity[lowercase]" name="password_complexity[lowercase]" {{ old('password_complexity[lowercase]', isset($settings['password_complexity']['lowercase']) && $settings['password_complexity']['lowercase'] ? 'checked' : '') }}>
        </div>
        <div class="form-group">
            <label for="password_complexity[numbers]">Chiffres requis</label>
            <input type="checkbox" class="form-control" id="password_complexity[numbers]" name="password_complexity[numbers]" {{ old('password_complexity[numbers]', isset($settings['password_complexity']['numbers']) && $settings['password_complexity']['numbers'] ? 'checked' : '') }}>
        </div>
        <div class="form-group">
            <label for="password_complexity[special_chars]">Caractères spéciaux requis</label>
            <input type="checkbox" class="form-control" id="password_complexity[special_chars]" name="password_complexity[special_chars]" {{ old('password_complexity[special_chars]', isset($settings['password_complexity']['special_chars']) && $settings['password_complexity']['special_chars'] ? 'checked' : '') }}>
        </div>



        <!-- Nombre d'historique des mots de passe -->
        <div class="form-group">
            <label for="password_history">Nombre d'anciens mots de passe à ne pas réutiliser</label>
            <input type="number" class="form-control" id="password_history" name="password_history" value="{{ old('password_history', $settings['password_history']) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
