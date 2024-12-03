@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Tableau de bord</h1>
            <p>Bienvenue sur le tableau de bord !</p>
            <ul class="list-group">
            <li class="list-group-item"><a href="{{ route('password.change') }}">Changer le mot de passe</a></li>                
                @if (Auth::user()->hasRole('Administrateur'))
                    <li class="list-group-item"><a href="{{ route('client.index') }}">Gérer les clients</a></li>
                    <li class="list-group-item"><a href="{{ route('admin.security-settings') }}">Paramètres de sécurité</a></li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection