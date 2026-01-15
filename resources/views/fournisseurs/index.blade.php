@extends('adminlte::page')

@section('title', 'Fournisseurs')

@section('content_header')
    <h1>Fournisseurs</h1>
@stop

@section('content')

<a href="{{ route('suppliers.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus"></i> Nouveau fournisseur
</a>

<div class="row fournisseurs-cards d-flex flex-wrap">
@forelse($fournisseurs as $fournisseur)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100 fournisseur-card position-relative">
            
            {{-- IMAGE + NOM --}}
            <div class="card-link" onclick="window.location='{{ route('suppliers.show', $fournisseur->id) }}'">
                <img src="{{ $fournisseur->photo ? asset('storage/'.$fournisseur->photo) : asset('images/default-supplier.png') }}"
                     class="card-img-top" style="height:160px; object-fit:cover;">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ $fournisseur->nom }}</h5>
                    @if($fournisseur->marque)
                        <span class="badge badge-info">{{ $fournisseur->marque }}</span>
                    @endif
                </div>
            </div>

            {{-- MENU 3 POINTS --}}
            <div class="fournisseur-menu position-absolute" style="top: 10px; right: 10px;">
                <button class="btn btn-light btn-sm rounded-circle shadow-sm menu-toggle" 
                        type="button" 
                        data-fournisseur-id="{{ $fournisseur->id }}">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="menu-dropdown shadow" id="menu-{{ $fournisseur->id }}" style="display: none;">
                    <a class="menu-item" href="{{ route('suppliers.show', $fournisseur->id) }}">
                        <i class="fas fa-eye text-info mr-2"></i> Voir fiche
                    </a>
                    <a class="menu-item" href="{{ route('suppliers.edit', $fournisseur->id) }}">
                        <i class="fas fa-pen text-warning mr-2"></i> Modifier
                    </a>
                    <hr class="my-1">
                    <form action="{{ route('suppliers.destroy', $fournisseur->id) }}" method="POST" class="delete-form">
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
@empty
    <p>Aucun fournisseur enregistré.</p>
@endforelse
</div>

{{-- Pagination --}}
{{ $fournisseurs->links() }}

@stop

@section('css')
<style>
/* Menu 3 points */
.fournisseur-menu .menu-toggle {
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

.fournisseur-menu .menu-toggle:hover {
    background: white;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.menu-dropdown {
    position: absolute;
    top: 40px;
    right: 0;
    background: white;
    border-radius: 12px;
    padding: 0.5rem;
    min-width: 180px;
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

.fournisseur-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.fournisseur-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}

.card-link {
    cursor: pointer;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Toggle menu
    $('.menu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var fournisseurId = $(this).data('fournisseur-id');
        var menu = $('#menu-' + fournisseurId);
        
        $('.menu-dropdown').not(menu).hide();
        menu.toggle();
    });

    // Fermer le menu si on clique ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.fournisseur-menu').length) {
            $('.menu-dropdown').hide();
        }
    });

    // Confirmation de suppression
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var form = $(this).closest('form');
        
        if(confirm('Voulez-vous vraiment supprimer ce fournisseur ?')) {
            form.submit();
        } else {
            $(this).closest('.menu-dropdown').hide();
        }
    });
});
</script>
@stop