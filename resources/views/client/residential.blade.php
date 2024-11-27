@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des clients résidentiels</h1>
    @if($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td>{{ $client->first_name }}</td>
                <td>{{ $client->last_name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection