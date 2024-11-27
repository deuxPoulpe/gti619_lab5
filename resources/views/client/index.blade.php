@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des clients</h1>
    @if($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="mb-3">
        <a href="{{ route('client.create') }}" class="btn btn-primary">Ajouter un client</a>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td>{{ $client['first_name'] }}</td>
                <td>{{ $client['last_name'] }}</td>
                <td>{{ $client['type'] == 'residential' ? 'Résidentiel' : 'D’affaire' }}</td>
                <td>
                    <a href="{{ route('client.edit', $client['id']) }}" class="btn btn-primary">Modifier</a>
                    <form action="{{ route('client.destroy', $client['id']) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection