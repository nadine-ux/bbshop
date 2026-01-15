@extends('adminlte::page')

@section('title', 'Catégories')

@section('content_header')
    <h1>Catégories</h1>
@stop

@section('content')

<a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nouvelle catégorie
</a>

<div class="row categories-cards d-flex flex-wrap">
@foreach($categories as $cat)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100 category-card position-relative" data-id="{{ $cat->id }}">
            
            {{-- IMAGE + NOM --}}
            <div class="card-link" onclick="window.location='{{ route('categories.show', $cat->id) }}'">
                <img src="{{ $cat->photo ? asset('storage/'.$cat->photo) : asset('images/default-category.png') }}"
                     class="card-img-top" style="height:160px; object-fit:cover;">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $cat->nom }}</h5>
                    @if($cat->articles_count ?? false)
                        <span class="badge badge-info">{{ $cat->articles_count }} articles</span>
                    @endif
                </div>
            </div>

            {{-- MENU SIMPLE AVEC TOGGLE MANUEL --}}
            <div class="category-menu position-absolute" style="top: 10px; right: 10px;">
                <button class="btn btn-light btn-sm rounded-circle shadow-sm menu-toggle" 
                        type="button" 
                        data-cat-id="{{ $cat->id }}">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="menu-dropdown shadow" id="menu-{{ $cat->id }}" style="display: none;">
                    <a class="menu-item" href="{{ route('categories.show', $cat->id) }}">
                        <i class="fas fa-eye text-info mr-2"></i> Voir
                    </a>
                    <a class="menu-item" href="{{ route('categories.edit', $cat->id) }}">
                        <i class="fas fa-pen text-warning mr-2"></i> Modifier
                    </a>
                    <hr class="my-1">
                    <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="menu-item text-danger delete-btn" style="width: 100%; text-align: left; background: none; border: none;">
                            <i class="fas fa-trash mr-2"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endforeach
</div>

@stop

@section('css')
<style>
/* Style du bouton menu */
.category-menu .menu-toggle {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    z-index: 100;
    position: relative;
}

.category-menu .menu-toggle:hover {
    background: white;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

/* Menu dropdown */
.menu-dropdown {
    position: absolute;
    top: 40px;
    right: 0;
    background: white;
    border-radius: 12px;
    padding: 0.5rem;
    min-width: 160px;
    z-index: 1000;
    border: 1px solid #e0e0e0;
}

.menu-item {
    display: block;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    color: #333;
}

.menu-item:hover {
    background: #f8f9fa;
    transform: translateX(4px);
    text-decoration: none;
    color: #333;
}

/* Card hover effect */
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}

.card-link {
    cursor: pointer;
}

/* Position relative pour le menu */
.category-menu {
    z-index: 100;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Toggle menu au clic
    $('.menu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var catId = $(this).data('cat-id');
        var menu = $('#menu-' + catId);
        
        // Fermer tous les autres menus
        $('.menu-dropdown').not(menu).hide();
        
        // Toggle le menu actuel
        menu.toggle();
        
        console.log('Menu toggled for category: ' + catId); // Debug
    });

    // Fermer le menu si on clique ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.category-menu').length) {
            $('.menu-dropdown').hide();
        }
    });

    // Empêcher la propagation sur le menu
    $('.menu-dropdown').on('click', function(e) {
        e.stopPropagation();
    });

    // Confirmation de suppression
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var form = $(this).closest('form');
        
        if(confirm('Voulez-vous vraiment supprimer cette catégorie ?')) {
            form.submit();
        } else {
            // Fermer le menu si annulé
            $(this).closest('.menu-dropdown').hide();
        }
    });

    // Empêcher le clic sur la carte quand on clique sur le menu
    $('.category-menu').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
@stop