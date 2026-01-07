@extends('adminlte::page')

@section('title','Modifier une entrée')

@section('content_header')
    <h1>Modifier l’entrée : {{ $entree->numero_bon }}</h1>
@stop

@section('content')
    <form action="{{ route('entrees.update',$entree) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="date_reception">Date de réception</label>
            <input type="date" name="date_reception" class="form-control" value="{{ old('date_reception',$entree->date_reception) }}" required>
        </div>

        <div class="form-group">
            <label for="numero_bon">Numéro de bon</label>
            <input type="text" name="numero_bon