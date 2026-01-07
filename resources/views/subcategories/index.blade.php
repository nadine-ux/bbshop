@extends('adminlte::page')

@section('title','Sous‑catégories')

@section('content_header')
    <h1>Liste des sous‑catégories</h1>
@stop

@section('content')
    <a href="{{ route('subcategories.create') }}" class="btn btn-primary mb-3">Nouvelle sous‑catégorie</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subcategories as $sub)
                <tr>
                    <td>{{ $sub->nom }}</td>
                    <td>{{ $sub->category->nom }}</td>
                    <td>
                        <a href="{{ route('subcategories.edit',$sub->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('subcategories.destroy',$sub->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette sous‑catégorie ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
