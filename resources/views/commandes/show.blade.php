@extends('adminlte::page')

@section('title','Commande #'.$commande->id)

@section('content_header')
    <h1>Détail de la commande #{{ $commande->id }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <p><strong>Article :</strong> {{ $commande->article->nom }}</p>
        <p><strong>Quantité cartons :</strong> {{ $commande->quantite_cartons }}</p>
        <p><strong>Quantité pièces :</strong> {{ $commande->quantite_pieces }}</p>
        <p><strong>Quantité totale :</strong> {{ $commande->quantite_total }}</p>
        <p><strong>
    