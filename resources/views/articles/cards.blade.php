@extends('adminlte::page')

@section('title', $category->nom)

@section('content_header')
    <h1>Articles : {{ $category->nom }}</h1>
@stop

@section('content')

<div class="row articles-cards d-flex flex-wrap">
@forelse($articles as $article)
    <div class="col-6 col-md-3 mb-4 d-flex">
        <div class="card h-100 shadow-sm w-100 article-card position-relative">

            <img src="{{ $article->photo 
                ? asset('storage/'.$article->photo) 
                : asset('images/default-product.png') }}"
                class="card-img-top"
                style="height:160px; object-fit:cover; cursor: pointer;"
                onclick="showArticleDetails({{ $article->id }})">

            <div class="card-body text-center">
                <h6 class="card-title">{{ $article->nom }}</h6>

                {{-- STOCK --}}
                @if($article->stock <= $article->quantite_minimale)
                    <span class="badge badge-danger">Stock bas : {{ $article->stock }}</span>
                @else
                    <span class="badge badge-success">Stock : {{ $article->stock }}</span>
                @endif
            </div>

            {{-- MENU 3 POINTS --}}
            <div class="article-menu position-absolute" style="top: 10px; right: 10px;">
                <button class="btn btn-light btn-sm rounded-circle shadow-sm menu-toggle" 
                        type="button" 
                        data-article-id="{{ $article->id }}">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="menu-dropdown shadow" id="menu-{{ $article->id }}" style="display: none;">
                    <a class="menu-item" href="javascript:void(0)" onclick="showArticleDetails({{ $article->id }})">
                        <i class="fas fa-eye text-info mr-2"></i> Voir détails
                    </a>
                    <a class="menu-item" href="{{ route('articles.edit', $article->id) }}">
                        <i class="fas fa-pen text-warning mr-2"></i> Modifier
                    </a>
                    <hr class="my-1">
                    <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="delete-form">
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
    <p>Aucun article dans cette catégorie.</p>
@endforelse
</div>

{{-- Pagination --}}
{{ $articles->links() }}

{{-- MODAL DÉTAILS ARTICLE --}}
<div class="modal fade" id="articleDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="fas fa-info-circle mr-2"></i>
                    Détails de l'article
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="articleDetailsContent">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-info"></i>
                    <p class="mt-3">Chargement...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
/* Menu 3 points */
.article-menu .menu-toggle {
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

.article-menu .menu-toggle:hover {
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

.article-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}

/* Style du modal */
.detail-row {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.detail-value {
    color: #212529;
}

.price-hidden {
    filter: blur(5px);
    user-select: none;
}

/* Historique */
.mouvement-item {
    padding: 10px;
    border-left: 3px solid #17a2b8;
    background: #f8f9fa;
    margin-bottom: 10px;
    border-radius: 4px;
}

.mouvement-item.entree {
    border-left-color: #28a745;
}

.mouvement-item.sortie {
    border-left-color: #dc3545;
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
        
        var articleId = $(this).data('article-id');
        var menu = $('#menu-' + articleId);
        
        $('.menu-dropdown').not(menu).hide();
        menu.toggle();
    });

    // Fermer le menu si on clique ailleurs
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.article-menu').length) {
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
        
        if(confirm('Voulez-vous vraiment supprimer cet article ?')) {
            form.submit();
        } else {
            $(this).closest('.menu-dropdown').hide();
        }
    });
});

// Fonction pour afficher les détails
function showArticleDetails(articleId) {
    // Fermer tous les menus
    $('.menu-dropdown').hide();
    
    // Ouvrir le modal
    $('#articleDetailsModal').modal('show');
    
    // Charger les détails via AJAX
    $.ajax({
        url: '/articles/' + articleId + '/details',
        method: 'GET',
        success: function(response) {
            $('#articleDetailsContent').html(response);
        },
        error: function() {
            $('#articleDetailsContent').html(
                '<div class="alert alert-danger">' +
                '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                'Erreur lors du chargement des détails.' +
                '</div>'
            );
        }
    });
}

// Toggle affichage du prix
function togglePrice() {
    var priceElement = $('#price-value');
    var icon = $('#toggle-price-icon');
    
    if(priceElement.hasClass('price-hidden')) {
        priceElement.removeClass('price-hidden');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        priceElement.addClass('price-hidden');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}
</script>
@stop