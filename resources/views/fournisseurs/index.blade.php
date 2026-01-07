@extends('adminlte::page')

@section('title','Fournisseurs')

@section('content_header')
    <h1>Fournisseurs</h1>
@stop

@section('content')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-3">Ajouter un fournisseur</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Marque</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Adresse</th>
                <th style="width:160px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fournisseurs as $supplier)
                <tr>
                    <td>{{ $supplier->nom }}</td>
                    <td>{{ $supplier->marque }}</td>
                    <td>{{ $supplier->telephone }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->adresse }}</td>
                    <td>
                        <a href="{{ route('suppliers.show',$supplier) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('suppliers.edit',$supplier) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('suppliers.destroy',$supplier) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce fournisseur ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Aucun fournisseur pour l’instant.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $fournisseurs->links() }}
@stop
