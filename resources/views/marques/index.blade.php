@extends('adminlte::page')

@section('title', 'Marques')

@section('content_header')
    <div class="header-modern">
        <div class="header-left">
            <h1 class="page-title">
                <i class="fas fa-tag"></i>
                Gestion des Marques
            </h1>
            <p class="page-subtitle">{{ $marques->total() }} marque(s) enregistrée(s)</p>
        </div>
        <a href="{{ route('marques.create') }}" class="btn-modern-add">
            <i class="fas fa-plus"></i>
            <span>Ajouter une marque</span>
        </a>
    </div>
@stop

@section('content')

{{-- Messages de succès/erreur --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show modern-alert" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

{{-- Filtres --}}
<div class="filter-card-modern">
    <form method="GET" action="{{ route('marques.index') }}" id="filterForm">
        <div class="row">
            <div class="col-md-10">
                <div class="form-group-modern">
                    <label><i class="fas fa-tag"></i> Rechercher une marque</label>
                    <input type="text" name="nom" class="form-control-modern" 
                           placeholder="Rechercher par nom..." 
                           value="{{ request('nom') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group-modern">
                    <label>&nbsp;</label>
                    <div class="btn-group-modern">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('marques.index') }}" class="btn-reset">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Tableau des marques --}}
<div class="table-card-modern">
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th width="60%">
                        <i class="fas fa-tag mr-1"></i> Nom de la marque
                    </th>
                    <th width="15%">
                        <i class="fas fa-box mr-1"></i> Articles
                    </th>
                    <th width="15%" class="text-center">
                        <i class="fas fa-cogs mr-1"></i> Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($marques as $marque)
                <tr>
                    <td class="font-weight-bold text-muted">{{ $marque->id }}</td>
                    <td>
                        <div class="marque-name">
                            <i class="fas fa-tag text-primary mr-2"></i>
                            <strong>{{ $marque->nom }}</strong>
                        </div>
                    </td>
                    <td>
                        <span class="badge-count">
                            <i class="fas fa-box"></i>
                            {{ $marque->articles->count() }} article(s)
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="action-buttons">
                            <a href="{{ route('marques.edit', $marque->id) }}" 
                               class="btn-action btn-edit" 
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <button class="btn-action btn-delete delete-btn" 
                                    data-id="{{ $marque->id }}"
                                    data-form="delete-form-{{ $marque->id }}"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                            
                            <form id="delete-form-{{ $marque->id }}" 
                                  action="{{ route('marques.destroy', $marque->id) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center empty-state">
                        <i class="fas fa-tag fa-3x mb-3"></i>
                        <p class="mb-0">Aucune marque trouvée</p>
                        <a href="{{ route('marques.create') }}" class="btn-modern-add mt-3">
                            <i class="fas fa-plus"></i> Ajouter une marque
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="pagination-modern">
    {{ $marques->links() }}
</div>

@stop

@section('css')
<style>
/* Header moderne */
.header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.header-left {
    display: flex;
    flex-direction: column;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title i {
    color: #FF6B35;
}

.page-subtitle {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin: 0.25rem 0 0 0;
}

.btn-modern-add {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-modern-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
}

/* Alerts */
.modern-alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

/* Filtres */
.filter-card-modern {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 1.5rem;
}

.form-group-modern label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: block;
}

.form-group-modern label i {
    color: #FF6B35;
    margin-right: 0.25rem;
}

.form-control-modern {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.form-control-modern:focus {
    border-color: #FF6B35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.1);
    outline: none;
}

.btn-group-modern {
    display: flex;
    gap: 0.5rem;
}

.btn-filter {
    flex: 1;
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-reset {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: #5a6268;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

/* Table */
.table-card-modern {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.table-modern {
    width: 100%;
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
}

.table-modern thead th {
    padding: 1.25rem;
    font-weight: 600;
    border: none;
}

.table-modern tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}

.table-modern tbody tr:hover {
    background: #f8f9fa;
}

.marque-name {
    display: flex;
    align-items: center;
    font-size: 1.1rem;
}

.badge-count {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Actions */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-action {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-edit {
    color: #f39c12;
}

.btn-edit:hover {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    transform: translateY(-2px);
}

.btn-delete {
    color: #e74c3c;
}

.btn-delete:hover {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    transform: translateY(-2px);
}

/* Empty state */
.empty-state {
    padding: 4rem 2rem !important;
    color: #7f8c8d;
}

.empty-state i {
    color: #FF6B35;
    opacity: 0.3;
}

/* Pagination */
.pagination-modern {
    margin-top: 1.5rem;
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
    color: #FF6B35;
    font-weight: 600;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.pagination-modern .page-link:hover {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
    transform: translateY(-2px);
}

.pagination-modern .page-item.active .page-link {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .header-modern {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-modern-add {
        width: 100%;
        justify-content: center;
    }
    
    .action-buttons {
        flex-wrap: wrap;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Confirmation de suppression
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        const formId = $(this).data('form');
        const form = $('#' + formId);
        
        if(confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette marque ?\n\nCette action est irréversible.')) {
            form.submit();
        }
    });
    
    // Auto-submit du formulaire de filtre en tapant
    let typingTimer;
    $('input[name="nom"]').on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            $('#filterForm').submit();
        }, 800);
    });
});
</script>
@stop