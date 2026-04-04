@extends('adminlte::page')

@section('title', $title ?? 'Catégories')

@section('content_header')
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
        <h1 style="font-size:20px;font-weight:500;margin:0;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-tags" style="color:#E60000;font-size:18px;"></i>
            {{ $title ?? 'Catégories' }}
        </h1>
        <small class="text-muted">Gestion des catégories de produits</small>
    </div>
    <a href="{{ route('categories.create') }}" 
       style="display:inline-flex;align-items:center;gap:6px;background:#E60000;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:500;text-decoration:none;">
        <i class="fas fa-plus" style="font-size:11px;"></i> Nouvelle catégorie
    </a>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

{{-- ===== BARRE RECHERCHE + TRI ===== --}}
<div style="background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:14px 16px;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;gap:10px;align-items:center;">

    <form method="GET" action="{{ route('categories.index') }}"
          style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;width:100%;">

        {{-- Barre de recherche --}}
        <div style="flex:1;min-width:200px;position:relative;">
            <i class="fas fa-search" 
               style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#adb5bd;font-size:12px;"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Rechercher une catégorie..."
                   autocomplete="off"
                   style="width:100%;padding:7px 10px 7px 30px;border:1px solid #dee2e6;border-radius:8px;background:#f8f9fa;font-size:13px;color:#212529;outline:none;">
        </div>

        {{-- Trier par --}}
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="font-size:12px;color:#6c757d;white-space:nowrap;">
                <i class="fas fa-sort fa-xs mr-1"></i>Trier par
            </span>

            {{-- Pills de tri --}}
            @php
                $currentSort = request('sort', 'nom');
                $currentDir  = request('direction', 'asc');
                $sorts = ['nom' => 'Nom', 'created_at' => 'Date création'];
            @endphp

            @foreach($sorts as $key => $label)
                @php
                    $isActive = $currentSort === $key;
                    $nextDir  = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
                @endphp
                <a href="{{ route('categories.index', array_merge(request()->query(), ['sort' => $key, 'direction' => $nextDir, 'search' => request('search')])) }}"
                   style="display:inline-flex;align-items:center;gap:4px;
                          background:{{ $isActive ? '#fff0f0' : '#f8f9fa' }};
                          border:1px solid {{ $isActive ? '#E60000' : '#dee2e6' }};
                          color:{{ $isActive ? '#A32D2D' : '#6c757d' }};
                          border-radius:20px;padding:5px 13px;font-size:12px;
                          font-weight:{{ $isActive ? '500' : '400' }};text-decoration:none;">
                    {{ $label }}
                    @if($isActive)
                        <i class="fas fa-arrow-{{ $currentDir === 'asc' ? 'up' : 'down' }}" 
                           style="font-size:9px;"></i>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Bouton réinitialiser (si filtres actifs) --}}
        @if(request()->hasAny(['search', 'sort', 'direction']))
            <a href="{{ route('categories.index') }}"
               style="display:inline-flex;align-items:center;gap:5px;background:#f8f9fa;border:1px solid #dee2e6;color:#6c757d;border-radius:8px;padding:6px 12px;font-size:12px;text-decoration:none;">
                <i class="fas fa-times" style="font-size:10px;"></i> Réinitialiser
            </a>
        @endif

        {{-- Input caché pour soumission auto via JS --}}
        <input type="hidden" name="sort" value="{{ request('sort', 'nom') }}">
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

    </form>
</div>

{{-- Compteur --}}
<div style="margin-bottom:.75rem;">
    <small class="text-muted">
        <strong style="color:#212529;">{{ $categories->count() }}</strong> catégorie(s) trouvée(s)
        @if(request('search'))
            pour "<strong>{{ request('search') }}</strong>"
            &mdash; <a href="{{ route('categories.index') }}" class="text-danger" style="font-size:11px;">
                <i class="fas fa-times-circle"></i> effacer
            </a>
        @endif
    </small>
</div>

{{-- ===== GRILLE DE CATÉGORIES ===== --}}
@if($categories->isNotEmpty())
<div class="categories-grid">
    @foreach($categories as $cat)
    <div class="category-card-wrapper">
        <div class="category-card-modern">

            {{-- Image --}}
            <div class="card-image-container" 
                 onclick="window.location='{{ route('categories.show', $cat->id) }}'">
                <img src="{{ $cat->photo ? asset('storage/'.$cat->photo) : asset('images/default-category.png') }}"
                     class="card-image"
                     alt="{{ $cat->nom }}">
                <div class="card-overlay"></div>

                {{-- Badge sous-catégories ou articles --}}
                @if(($cat->children_count ?? 0) > 0)
                <div class="articles-badge">
                    <i class="fas fa-layer-group" style="font-size:10px;"></i>
                    <span>{{ $cat->children_count }} s/cat</span>
                </div>
                @elseif(isset($cat->articles_count) && $cat->articles_count > 0)
                <div class="articles-badge">
                    <i class="fas fa-cube" style="font-size:10px;"></i>
                    <span>{{ $cat->articles_count }}</span>
                </div>
                @endif
            </div>

            {{-- Contenu --}}
            <div class="card-content" 
                 onclick="window.location='{{ route('categories.show', $cat->id) }}'">
                <h3 class="card-title-modern">{{ $cat->nom }}</h3>
                @if($cat->description)
                    <p class="card-description">{{ Str::limit($cat->description, 60) }}</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="card-actions">
                <a href="{{ route('categories.show', $cat->id) }}" 
                   class="action-btn action-view" title="Voir">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('categories.edit', $cat->id) }}" 
                   class="action-btn action-edit" title="Modifier">
                    <i class="fas fa-pen"></i>
                </a>
                <button type="button" 
                        class="action-btn action-delete delete-btn"
                        data-form="delete-form-{{ $cat->id }}" 
                        title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
                <form id="delete-form-{{ $cat->id }}"
                      action="{{ route('categories.destroy', $cat->id) }}"
                      method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    </div>
    @endforeach
</div>

{{-- ===== ÉTAT VIDE ===== --}}
@else
<div class="empty-state">
    <i class="fas fa-tags"></i>
    @if(request('search'))
        <h3>Aucun résultat</h3>
        <p>Aucune catégorie ne correspond à "<strong>{{ request('search') }}</strong>"</p>
        <a href="{{ route('categories.index') }}" 
           style="display:inline-flex;align-items:center;gap:6px;background:#E60000;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:500;text-decoration:none;">
            <i class="fas fa-times"></i> Effacer la recherche
        </a>
    @else
        <h3>Aucune catégorie</h3>
        <p>Commencez par créer votre première catégorie</p>
        <a href="{{ route('categories.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:#E60000;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:500;text-decoration:none;">
            <i class="fas fa-plus"></i> Créer une catégorie
        </a>
    @endif
</div>
@endif

@stop

@section('css')
<style>
.content-wrapper { background: #f5f6fa !important; }

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
}

.category-card-modern {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e9ecef;
    transition: transform .3s ease, box-shadow .3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.category-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(230,0,0,.12);
}

.card-image-container {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
    cursor: pointer;
}
.card-image {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.category-card-modern:hover .card-image { transform: scale(1.08); }

.card-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,.3) 100%);
    opacity: 0; transition: opacity .3s ease;
}
.category-card-modern:hover .card-overlay { opacity: 1; }

.articles-badge {
    position: absolute; top: 10px; right: 10px;
    background: #E60000; color: white;
    border-radius: 12px; padding: 3px 10px;
    font-size: 11px; font-weight: 600;
    display: flex; align-items: center; gap: 4px;
}

.card-content {
    padding: 1rem 1.25rem; flex: 1; cursor: pointer;
}
.card-title-modern {
    font-size: 15px; font-weight: 600; color: #2c3e50;
    margin: 0 0 4px; line-height: 1.3;
}
.card-description {
    color: #7f8c8d; font-size: 12px;
    line-height: 1.5; margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-actions {
    display: flex; gap: 6px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}
.action-btn {
    flex: 1; padding: 7px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white; color: #6c757d;
    font-size: 12px; cursor: pointer;
    transition: all .25s ease;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
}
.action-btn:hover { transform: translateY(-1px); text-decoration: none; }
.action-view  { color: #185FA5; border-color: #bee3f8; background: #ebf8ff; }
.action-edit  { color: #854F0B; border-color: #fbd38d; background: #fffbeb; }
.action-delete{ color: #A32D2D; border-color: #fed7d7; background: #fff5f5; }
.action-view:hover  { background: #185FA5; color: white; border-color: #185FA5; }
.action-edit:hover  { background: #E60000; color: white; border-color: #E60000; }
.action-delete:hover{ background: #A32D2D; color: white; border-color: #A32D2D; }

.empty-state {
    text-align: center; padding: 4rem 2rem;
    background: white; border-radius: 16px;
    border: 1px solid #e9ecef;
}
.empty-state i  { font-size: 4rem; color: #E60000; opacity: .2; display: block; margin-bottom: 1rem; }
.empty-state h3 { font-size: 1.4rem; font-weight: 600; color: #2c3e50; margin-bottom: .4rem; }
.empty-state p  { color: #7f8c8d; margin-bottom: 1.5rem; }

.category-card-wrapper {
    animation: fadeUp .4s ease both;
}
@for($i = 1; $i <= 12; $i++)
.category-card-wrapper:nth-child({{ $i }}) { animation-delay: {{ $i * 0.05 }}s; }
@endfor

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .categories-grid { grid-template-columns: repeat(2, 1fr); gap: .75rem; }
    .card-image-container { height: 130px; }
    .card-title-modern { font-size: 13px; }
}
@media (max-width: 480px) {
    .categories-grid { grid-template-columns: 1fr; }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function () {

    // Soumission auto à la frappe (debounce 400ms)
    let searchTimer;
    $('input[name="search"]').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            $(this).closest('form').submit();
        }, 400);
    });

    // Confirmation suppression
    $('.delete-btn').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const formId = $(this).data('form');
        if (confirm('Voulez-vous vraiment supprimer cette catégorie ?\nCette action est irréversible.')) {
            $('#' + formId).submit();
        }
    });

});
</script>
@stop