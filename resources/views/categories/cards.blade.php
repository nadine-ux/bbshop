@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')

{{-- Bouton pour ajouter une nouvelle catégorie --}}
<a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nouvelle catégorie
</a>

{{-- Grille responsive --}}
<div class="row categories-cards d-flex flex-wrap">
@foreach($categories as $cat)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <a href="{{ route('categories.show', $cat->id) }}" class="text-decoration-none text-dark w-100">

            <div class="card h-100 shadow-sm">
                <img src="{{ $cat->photo 
                    ? asset('storage/'.$cat->photo) 
                    : asset('images/default-category.png') }}"
                    class="card-img-top"
                    style="height:160px; object-fit:cover;">

                <div class="card-body text-center">
                    <h5 class="card-title">{{ $cat->nom }}</h5>
                    @if($cat->articles_count ?? false)
                        <span class="badge badge-info">{{ $cat->articles_count }} articles</span>
                    @endif
                </div>
            </div>

        </a>
    </div>
@endforeach
</div>

@stop
