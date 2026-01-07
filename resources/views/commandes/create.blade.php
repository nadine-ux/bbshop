@extends('adminlte::page')

@section('title','Nouvelle commande')

@section('content_header')
    <h1>Nouvelle commande</h1>
@stop

@section('content')
<form action="{{ route('commandes.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Article</label>
        <select name="article_id" class="form-control">
            @foreach($articles as $article)
                <option value="{{ $article->id }}">{{ $article->nom }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label>Quantité cartons</label>
        <input type="number" name="quantite_cartons" class="form-control">
    </div>
    <div class="form-group">
        <label>Quantité pièces</label>
        <input type="number" name="quantite_pieces" class="form-control">
    </div>
    <div class="form-group">
        <label>Remarque</label>
        <textarea name="remarque" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label>Date</label>
        <input type="date" name="date" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Créer</button>
</form>
@stop
