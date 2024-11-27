@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Modifier un client</h1>
    <form method="POST" action="{{ route('client.update', $client->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="first_name">Prénom</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $client->first_name }}" required>
        </div>
        <div class="form-group">
            <label for="last_name">Nom</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $client->last_name }}" required>
        </div>
        <div class="form-group">
            <label for="type">Type de client</label>
            <select class="form-control" id="type" name="type" required>
                <option value="residential" {{ $client->type == 'residential' ? 'selected' : '' }}>Résidentiel</option>
                <option value="business" {{ $client->type == 'business' ? 'selected' : '' }}>D’affaire</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
