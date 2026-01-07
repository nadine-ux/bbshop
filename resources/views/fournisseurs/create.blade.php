@extends('adminlte::page')

@section('title','Ajouter un fournisseur')

@section('content_header')
    <h1>Ajouter un fournisseur</h1>
@stop

@section('content')
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nom">Nom du fournisseur</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="marque">Marque</label>
            <input type="text" name="marque" class="form-control">
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" name="telephone" class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="form-group">
            <label for="adresse">Adresse</label>
            <textarea name="adresse" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
