@extends('adminlte::page')

@section('title','Commandes')

@section('content_header')
    <h1>Liste des commandes</h1>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('commandes.create') }}" class="btn btn-primary mb-3">Nouvelle commande</a>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Article</th>
                        <th>Quantité totale</th>
                        <th>Gestionnaire</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandes as $commande)
                        <tr>
                            <td>{{ $commande->id }}</td>
                            <td>{{ $commande->article->nom }}</td>
                            <td>{{ $commande->quantite_total }}</td>
                            <td>{{ $commande->user->name }}</td>
                            <td>{{ $commande->statut }}</td>
                            <td>{{ $commande->date }}</td>
                            <td>
                                <a href="{{ route('commandes.show',$commande) }}" class="btn btn-info btn-sm">Voir</a>
                                <a href="{{ route('commandes.edit',$commande) }}" class="btn btn-warning btn-sm">Modifier</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $commandes->links() }}
        </div>
    </div>
@stop
