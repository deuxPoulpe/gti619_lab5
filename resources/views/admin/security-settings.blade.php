@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Paramètres de sécurité</h1>
    <form method="POST" action="">
        @csrf
        @method('PUT')
        
        <!-- Protection contre les attaques massives -->
        <h2 class="my-4">Protection contre les attaques massives</h2>
        <div class="form-group">
            <label for="max_attempts">Nombre de tentatives d’authentification maximal</label>
            <input type="number" class="form-control" id="max_attempts" name="max_attempts" required>
        </div>
        <div class="form-group">
            <label for="delay_between_attempts">Délai d’attente entre chaque tentative échouée (en secondes)</label>
            <input type="number" class="form-control" id="delay_between_attempts" name="delay_between_attempts" required>
        </div>

        <!-- Gestion du mot de passe -->
        <h2 class="my-4">Gestion du mot de passe</h2>
        <div class="form-group">
            <label for="password_expiry">Durée de validité du mot de passe (en jours)</label>
            <input type="number" class="form-control" id="password_expiry" name="password_expiry" required>
        </div>

        <!-- Norme de complexité pour les mots de passe -->
        <h2 class="my-4">Normes de complexité pour les mots de passe</h2>
        <div class="form-group">
            <label for="password_complexity">Complexité du mot de passe</label>
            <input type="text" class="form-control" id="password_complexity" name="password_complexity" required>
        </div>
        <div class="form-group">
            <label for="password_history">Impossibilité d’utiliser un ancien mot de passe parmi les x derniers</label>
            <input type="number" class="form-control" id="password_history" name="password_history" required>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Enregistrer</button>
    </form>
</div>
@endsection