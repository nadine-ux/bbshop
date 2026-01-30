@extends('adminlte::page')

@section('title', $category->nom)

@section('content_header')
    <div class="header-modern-articles">
        <div class="header-left">
            <a href="{{ route('categories.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="header-info">
                <h1 class="page-title-articles">
                    <i class="fas fa-cube"></i>
                    {{ $category->nom }}
                </h1>
                <p class="page-subtitle">{{ $articles->total() }} article(s) disponible(s)</p>
            </div>
        </div>
        <a href="{{ route('articles.create') }}" class="btn-modern-add">
            <i class="fas fa-plus"></i>
            <span>Ajouter un article</span>
        </a>
        <a href="{{ route('categories.create') }}" class="btn-modern-add">
            <i class="fas fa-plus"></i>
            <span>Ajouter une categories </span>
        </a>
    </div>
@stop

@section('content')

<div class="articles-grid-modern">
    @forelse($articles as $article)
    <div class="article-card-wrapper">
        <div class="article-card-modern" data-id="{{ $article->id }}">

            {{-- Image avec badge stock --}}
            <div class="card-image-box" onclick="showArticleDetails({{ $article->id }})">
                <img src="{{ $article->photo ? asset('storage/'.$article->photo) : asset('images/default-product.png') }}"
                     class="article-image" 
                     alt="{{ $article->nom }}">
                
                {{-- Badge stock --}}
                @if($article->stock <= $article->quantite_minimale)
                    <div class="stock-badge stock-low">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>{{ $article->stock }}</span>
                    </div>
                @else
                    <div class="stock-badge stock-ok">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $article->stock }}</span>
                    </div>
                @endif

                {{-- Overlay --}}
                <div class="card-overlay-article">
                    <button class="quick-view-btn" onclick="event.stopPropagation(); showArticleDetails({{ $article->id }})">
                        <i class="fas fa-eye"></i>
                        Voir détails
                    </button>
                </div>
            </div>

            {{-- Contenu --}}
            <div class="card-body-modern" onclick="showArticleDetails({{ $article->id }})">
                <h3 class="article-title">{{ $article->nom }}</h3>
                
                <div class="article-meta">
                    @if($article->code_barre)
                    <span class="meta-item">
                        <i class="fas fa-barcode"></i>
                        {{ $article->code_barre }}
                    </span>
                    @endif
                    
                    @if($article->prix_vente)
                    <span class="meta-price">
                        {{ number_format($article->prix_vente, 2) }} DZD
                    </span>
                    @endif
                </div>
            </div>

            {{-- Actions rapides --}}
            <div class="card-actions-modern">
                <button class="action-btn-modern action-view" 
                        onclick="showArticleDetails({{ $article->id }})"
                        title="Voir détails">
                    <i class="fas fa-eye"></i>
                </button>
                <a href="{{ route('articles.edit', $article->id) }}" 
                   class="action-btn-modern action-edit"
                   title="Modifier">
                    <i class="fas fa-pen"></i>
                </a>
                <button class="action-btn-modern action-delete delete-btn" 
                        data-id="{{ $article->id }}"
                        data-form="delete-form-{{ $article->id }}"
                        title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
                
                <form id="delete-form-{{ $article->id }}" 
                      action="{{ route('articles.destroy', $article->id) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    </div>
    @empty
    <div class="empty-state-articles">
        <i class="fas fa-box-open"></i>
        <h3>Aucun article</h3>
        <p>Cette catégorie ne contient pas encore d'articles</p>
        <a href="{{ route('articles.create') }}" class="btn-modern-add">
            <i class="fas fa-plus"></i>
            <span>Ajouter un article</span>
        </a>
    </div>
    @endforelse
</div>

{{-- Pagination moderne --}}
<div class="pagination-modern">
    {{ $articles->links() }}
</div>

{{-- MODAL DÉTAILS ARTICLE --}}
<div class="modal fade" id="articleDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modern-modal">
            <div class="modal-header-modern">
                <h5 class="modal-title-modern">
                    <i class="fas fa-info-circle"></i>
                    Détails de l'article
                </h5>
                <button type="button" class="close-modern" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-modern" id="articleDetailsContent">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Chargement des détails...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
/* ============================================
   HEADER ARTICLES MODERNE
   ============================================ */
.header-modern-articles {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-back {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;

    color: white;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-back:hover {
    transform: translateX(-4px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
}

.header-info {
    display: flex;
    flex-direction: column;
}

.page-title-articles {
    font-size: 1.75rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title-articles i {
    color: #FF6B35;
}

.page-subtitle {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin: 0.25rem 0 0 0;
}

.btn-modern-add {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-modern-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
    background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
}

/* ============================================
   GRILLE ARTICLES
   ============================================ */
.articles-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.5rem;
    padding: 0.5rem 0;
}

/* ============================================
   CARTE ARTICLE MODERNE
   ============================================ */
.article-card-modern {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.article-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(255, 107, 53, 0.15);
}

/* Image Container */
.card-image-box {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    cursor: pointer;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.article-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.article-card-modern:hover .article-image {
    transform: scale(1.1) rotate(2deg);
}

/* Overlay */
.card-overlay-article {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.article-card-modern:hover .card-overlay-article {
    opacity: 1;
}

.quick-view-btn {
    background: white;
    color: #FF6B35;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transform: translateY(20px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.article-card-modern:hover .quick-view-btn {
    transform: translateY(0);
}

.quick-view-btn:hover {
    background: #FF6B35;
    color: white;
    transform: translateY(-2px) scale(1.05);
}

/* Badge Stock */
.stock-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    animation: fadeInDown 0.5s ease;
}

.stock-badge i {
    font-size: 0.75rem;
}

.stock-low {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    animation: pulse 2s infinite;
}

.stock-ok {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Contenu */
.card-body-modern {
    padding: 1.25rem;
    flex: 1;
    cursor: pointer;
}

.article-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #7f8c8d;
    font-size: 0.85rem;
}

.meta-item i {
    color: #FF8C42;
}

.meta-price {
    font-size: 1.25rem;
    font-weight: 800;
    color: #FF6B35;
    background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 140, 66, 0.1));
    padding: 0.5rem 1rem;
    border-radius: 10px;
    display: inline-block;
    margin-top: 0.5rem;
}

/* ============================================
   ACTIONS MODERNES
   ============================================ */
.card-actions-modern {
    display: flex;
    padding: 0.75rem;
    gap: 0.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.action-btn-modern {
    flex: 1;
    padding: 0.75rem;
    border: none;
    border-radius: 10px;
    background: white;
    color: #6c757d;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.action-btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.action-view {
    color: #3498db;
}

.action-view:hover {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.action-edit {
    color: #f39c12;
}

.action-edit:hover {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
}

.action-delete {
    color: #e74c3c;
}

.action-delete:hover {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

/* ============================================
   MODAL MODERNE
   ============================================ */
.modern-modal {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header-modern {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
    padding: 1.5rem 2rem;
    border: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title-modern {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.close-modern {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-modern:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.modal-body-modern {
    padding: 2rem;
}

/* Loading Spinner */
.loading-spinner {
    text-align: center;
    padding: 3rem;
}

.spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto 1rem;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #ff3535ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ============================================
   ÉTAT VIDE
   ============================================ */
.empty-state-articles {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.empty-state-articles i {
    font-size: 5rem;
    color: #ff3535ff;
    opacity: 0.3;
    margin-bottom: 1.5rem;
}

.empty-state-articles h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.empty-state-articles p {
    color: #7f8c8d;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

/* ============================================
   PAGINATION MODERNE
   ============================================ */
.pagination-modern {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination-modern .pagination {
    gap: 0.5rem;
}

.pagination-modern .page-link {
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.25rem;
    color: #ff3535ff;
    font-weight: 600;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.pagination-modern .page-link:hover {
    background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.pagination-modern .page-item.active .page-link {
    background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;

    
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.article-card-wrapper {
    animation: fadeInUp 0.5s ease forwards;
}

.article-card-wrapper:nth-child(1) { animation-delay: 0.05s; }
.article-card-wrapper:nth-child(2) { animation-delay: 0.1s; }
.article-card-wrapper:nth-child(3) { animation-delay: 0.15s; }
.article-card-wrapper:nth-child(4) { animation-delay: 0.2s; }
.article-card-wrapper:nth-child(5) { animation-delay: 0.25s; }
.article-card-wrapper:nth-child(6) { animation-delay: 0.3s; }

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .articles-grid-modern {
          grid-template-columns: repeat(2, 1fr) !important; 
        gap: 1rem;
    }
    
    .card-image-box {
        height: 160px;
    }
    
    .card-body-modern {
        padding: 1rem;
    }
    
    .article-title {
        font-size: 1rem;
    }
    
    .header-modern-articles {
        flex-direction: column;
        align-items: stretch;
    }
    
    .header-left {
        width: 100%;
    }
    
    .btn-modern-add {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .articles-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .card-actions-modern {
        gap: 0.4rem;
        padding: 0.5rem;
    }
    
    .action-btn-modern {
        padding: 0.6rem;
        font-size: 0.9rem;
    }
}

/* Content wrapper */
.content-wrapper {
    background: #f5f6fa !important;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Confirmation de suppression
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const formId = $(this).data('form');
        const form = $('#' + formId);
        
        if(confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet article ?\n\nCette action est irréversible.')) {
            form.submit();
        }
    });
});

// Fonction pour afficher les détails
function showArticleDetails(articleId) {
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
                '<div class="alert alert-danger" style="border-radius: 12px;">' +
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