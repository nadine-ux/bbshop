@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')

<a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nouvelle catégorie
</a>

<div class="row categories-cards d-flex flex-wrap">
@foreach($categories as $cat)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100 category-card position-relative">
            <a href="{{ route('categories.show', $cat->id) }}" class="text-decoration-none text-dark card-link">
                <img src="{{ $cat->photo ? asset('storage/'.$cat->photo) : asset('images/default-category.png') }}"
                     class="card-img-top" style="height:160px; object-fit:cover;">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $cat->nom }}</h5>
                    @if($cat->articles_count ?? false)
                        <span class="badge badge-info">{{ $cat->articles_count }} articles</span>
                    @endif
                </div>
            </a>

            {{-- Actions overlay (cachées par défaut) --}}
            <div class="card-actions position-absolute top-0 end-0 p-2">
                <a href="{{ route('categories.edit', $cat->id) }}" class="text-warning me-2">
                    <i class="fas fa-pen fa-lg"></i>
                </a>
               <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="button" class="btn p-0 text-danger" onclick="if(confirm('Voulez-vous vraiment supprimer ?')) this.form.submit()">
        <i class="fas fa-trash fa-lg"></i>
    </button>
</form>

            </div>

        </div>
    </div>
@endforeach
</div>

@stop

@section('css')
<style>
/* Overlay caché par défaut */
.category-card .card-actions {
    display: none;
}

/* Desktop hover -> montrer les icônes */
@media (hover: hover) and (pointer: fine) {
    .category-card:hover .card-actions {
        display: flex;
        flex-direction: row;
        gap: 0.5rem;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Mobile long press
    var timer;
    $('.category-card').on('touchstart', function(e) {
        var card = $(this);
        timer = setTimeout(function() {
            card.find('.card-actions').fadeToggle();
        }, 600); // 600ms long press
    }).on('touchend touchmove touchcancel', function() {
        clearTimeout(timer);
    });
});
</script>
@stop
