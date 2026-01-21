@extends('adminlte::page')

@section('title', 'Catégories')

@section('content_header')
    <div class="header-modern">
        <h1 class="page-title">
            <i class="fas fa-tags"></i> Catégories
        </h1>
        <a href="{{ route('categories.create') }}" class="btn-modern-primary">
            <i class="fas fa-plus"></i>
            <span>Nouvelle catégorie</span>
        </a>
    </div>
@stop

@section('content')

<div class="categories-grid">
    @foreach($categories as $cat)
    <div class="category-card-wrapper">
        <div class="category-card-modern" data-id="{{ $cat->id }}">
            
            {{-- Image avec overlay --}}
            <div class="card-image-container" onclick="window.location='{{ route('categories.show', $cat->id) }}'">
                <img src="{{ $cat->photo ? asset('storage/'.$cat->photo) : asset('images/default-category.png') }}"
                     class="card-image" 
                     alt="{{ $cat->nom }}">
                <div class="card-overlay"></div>
                
                {{-- Badge compteur --}}
                @if($cat->articles_count ?? false)
                <div class="articles-badge">
                    <i class="fas fa-cube"></i>
                    <span>{{ $cat->articles_count }}</span>
                </div>
                @endif
            </div>

            {{-- Contenu --}}
            <div class="card-content" onclick="window.location='{{ route('categories.show', $cat->id) }}'">
                <h3 class="card-title-modern">{{ $cat->nom }}</h3>
                @if($cat->description)
                <p class="card-description">{{ Str::limit($cat->description, 60) }}</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="card-actions">
                <a href="{{ route('categories.show', $cat->id) }}" class="action-btn action-view" title="Voir">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('categories.edit', $cat->id) }}" class="action-btn action-edit" title="Modifier">
                    <i class="fas fa-pen"></i>
                </a>
                <button type="button" class="action-btn action-delete delete-btn" 
                        data-id="{{ $cat->id }}"
                        data-form="delete-form-{{ $cat->id }}"
                        title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
                
                <form id="delete-form-{{ $cat->id }}" 
                      action="{{ route('categories.destroy', $cat->id) }}" 
                      method="POST" 
                      style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    </div>
    @endforeach
</div>

{{-- Message si vide --}}
@if($categories->isEmpty())
<div class="empty-state">
    <i class="fas fa-tags"></i>
    <h3>Aucune catégorie</h3>
    <p>Commencez par créer votre première catégorie</p>
    <a href="{{ route('categories.create') }}" class="btn-modern-primary">
        <i class="fas fa-plus"></i>
        <span>Créer une catégorie</span>
    </a>
</div>
@endif

@stop

@section('css')
<style>
/* ============================================
   HEADER MODERNE
   ============================================ */
.header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title i {
    color: #FF6B35;
    font-size: 1.75rem;
}

.btn-modern-primary {
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

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
}

/* ============================================
   GRILLE DE CATÉGORIES
   ============================================ */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 0.5rem 0;
}

/* ============================================
   CARTE CATÉGORIE MODERNE
   ============================================ */
.category-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.category-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(255, 107, 53, 0.2);
}

/* Image Container */
.card-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    cursor: pointer;
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.category-card-modern:hover .card-image {
    transform: scale(1.1);
}

/* Overlay gradient */
.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, 
        transparent 0%, 
        rgba(0, 0, 0, 0.1) 50%,
        rgba(0, 0, 0, 0.4) 100%
    );
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card-modern:hover .card-overlay {
    opacity: 1;
}

/* Badge compteur articles */
.articles-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
    padding: 0.5rem 0.875rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
    backdrop-filter: blur(10px);
    animation: fadeInDown 0.5s ease;
}

.articles-badge i {
    font-size: 0.75rem;
}

/* Contenu */
.card-content {
    padding: 1.25rem 1.5rem;
    flex: 1;
    cursor: pointer;
}

.card-title-modern {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-description {
    color: #7f8c8d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ============================================
   ACTIONS MODERNES
   ============================================ */
.card-actions {
    display: flex;
    padding: 0.75rem;
    gap: 0.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.action-btn {
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

.action-btn:hover {
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
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    color: white;
}

/* ============================================
   ÉTAT VIDE
   ============================================ */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.empty-state i {
    font-size: 5rem;
    color: #FF6B35;
    opacity: 0.3;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #7f8c8d;
    font-size: 1.1rem;
    margin-bottom: 2rem;
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

.category-card-wrapper {
    animation: fadeInUp 0.5s ease forwards;
}

.category-card-wrapper:nth-child(1) { animation-delay: 0.05s; }
.category-card-wrapper:nth-child(2) { animation-delay: 0.1s; }
.category-card-wrapper:nth-child(3) { animation-delay: 0.15s; }
.category-card-wrapper:nth-child(4) { animation-delay: 0.2s; }
.category-card-wrapper:nth-child(5) { animation-delay: 0.25s; }
.category-card-wrapper:nth-child(6) { animation-delay: 0.3s; }

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem;
    }
    
    .card-image-container {
        height: 140px;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .card-title-modern {
        font-size: 1.1rem;
    }
    
    .header-modern {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .btn-modern-primary {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .card-actions {
        gap: 0.4rem;
        padding: 0.5rem;
    }
    
    .action-btn {
        padding: 0.6rem;
        font-size: 0.9rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .category-card-modern {
        background: #2c3e50;
    }
    
    .card-title-modern {
        color: #ecf0f1;
    }
    
    .card-description {
        color: #bdc3c7;
    }
    
    .card-actions {
        background: #34495e;
        border-top-color: #415568;
    }
    
    .action-btn {
        background: #415568;
        color: #bdc3c7;
    }
}

/* Content wrapper background */
.content-wrapper {
    background: #f5f6fa !important;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Confirmation de suppression avec SweetAlert style
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const formId = $(this).data('form');
        const form = $('#' + formId);
        
        // Confirmation moderne
        if(confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette catégorie ?\n\nCette action est irréversible.')) {
            form.submit();
        }
    });
    
    // Animation au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.category-card-wrapper').forEach(card => {
        observer.observe(card);
    });
});
</script>
@stop