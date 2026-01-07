@extends('adminlte::page')

@section('title','Modifier un fournisseur')

@section('content_header')
    <h1>Modifier le fournisseur : {{ $supplier->nom }}</h1>
@stop

@section('content')
    <form action="{{ route('suppliers.update',$supplier) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nom">Nom du fournisseur</label>
            <input type="text" name="nom" class="form-control" value="{{ old('nom',$supplier->nom) }}" required>
        </div>

        <div class="form-group">
            <label for="marque">Marque</label>
            <input type="text" name="marque" class="form-control" value="{{ old('marque',$supplier->marque) }}">
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" class="form-control" value="{{ old('telephone',$supplier->telephone) }}">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$supplier->email) }}">
        </div>

        <div class="form-group">
            <label for="adresse">Adresse</label>
            <textarea name="adresse" class="form-control">{{ old('adresse',$supplier->adresse) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
