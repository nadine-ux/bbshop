@extends('adminlte::page')

@section('title','Catégories')

@section('content_header')
    <h1>Liste des catégories</h1>
@stop

@section('content')
    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">Nouvelle catégorie</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie parente</th>
                <th>Sous‑catégories</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
                <tr>
                    <td>{{ $cat->nom }}</td>
                    <td>{{ $cat->parent?->nom ?? '—' }}</td>
                    <td>
                        @if($cat->children->count())
                            <ul>
                                @foreach($cat->children as $child)
                                    <li>
                                        {{ $child->nom }}
                                        {{-- Afficher aussi les sous‑sous‑catégories si elles existent --}}
                                        @if($child->children->count())
                                            <ul>
                                                @foreach($child->children as $subchild)
                                                    <li>{{ $subchild->nom }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
