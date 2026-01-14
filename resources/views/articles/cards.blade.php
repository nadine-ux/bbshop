@extends('adminlte::page')

@section('title', $category->nom)

@section('content_header')
    <h1>Articles : {{ $category->nom }}</h1>
@stop

@section('content')

<div class="row articles-cards d-flex flex-wrap">
@forelse($articles as $article)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100">

            <img src="{{ $article->photo 
                ? asset('storage/'.$article->photo) 
                : asset('images/default-product.png') }}"
                class="card-img-top"
                style="height:160px; object-fit:cover;">

            <div class="card-body text-center">
                <h6 class="card-title">{{ $article->nom }}</h6>

                {{-- STOCK --}}
                @if($article->stock <= $article->quantite_minimale)
                    <span class="badge badge-danger">Stock bas : {{ $article->stock }}</span>
                @else
                    <span class="badge badge-success">Stock : {{ $article->stock }}</span>
                @endif
            </div>
        </div>
    </div>
@empty
    <p>Aucun article dans cette catégorie.</p>
@endforelse
</div>

{{-- Pagination --}}
{{ $articles->links() }}

@stop
