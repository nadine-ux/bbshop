@extends('adminlte::page')

@section('title', 'Tableau de bord')

@section('content_header')
@stop

@section('css')
    <style>
        /* ============================================
           SIDEBAR MODERNE - BB Shopping
           ============================================ */

        /* Sidebar principale */
        .main-sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%) !important;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.1) !important;
        }

        /* Logo/Brand */
        .brand-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-bottom: none !important;
            padding: 1rem 1.25rem !important;
            transition: all 0.3s ease !important;
        }

        .brand-link:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
        }

        .brand-text {
            color: white !important;
            font-weight: 700 !important;
            font-size: 1.2rem !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }

        .brand-image {
            opacity: 1 !important;
            filter: brightness(1.2) !important;
        }

        /* Sidebar menu */
        .sidebar {
            padding-top: 0.5rem !important;
        }

        /* Headers de section */
        .nav-header {
            color: #95a5a6 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 1px !important;
            padding: 1rem 1rem 0.5rem 1rem !important;
            margin-top: 0.5rem !important;
            text-transform: uppercase !important;
        }

        /* Items du menu */
        .nav-sidebar .nav-item {
            margin-bottom: 0.25rem !important;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            border-radius: 8px !important;
            margin: 0 0.5rem !important;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        /* Effet de fond au survol */
        .nav-sidebar .nav-link::before {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            height: 100% !important;
            width: 3px !important;
            background: linear-gradient(180deg, #667eea, #764ba2) !important;
            transform: scaleY(0) !important;
            transition: transform 0.3s ease !important;
        }

        .nav-sidebar .nav-link:hover::before {
            transform: scaleY(1) !important;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transform: translateX(4px) !important;
        }

        /* Item actif */
        .nav-sidebar .nav-link.active,
        .nav-sidebar .nav-link.active:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
        }

        .nav-sidebar .nav-link.active::before {
            transform: scaleY(1) !important;
        }

        /* Icônes du menu */
        .nav-sidebar .nav-icon {
            margin-right: 0.75rem !important;
            font-size: 1.1rem !important;
            width: 1.5rem !important;
            text-align: center !important;
            transition: all 0.3s ease !important;
        }

        .nav-sidebar .nav-link:hover .nav-icon {
            transform: scale(1.1) !important;
        }

        .nav-sidebar .nav-link.active .nav-icon {
            color: white !important;
        }

        /* Scrollbar personnalisée pour la sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px !important;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1) !important;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.5) !important;
            border-radius: 10px !important;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.8) !important;
        }

        /* Content header color */
        .content-header h1 {
            color: white !important;
        }
    </style>
@stop

@section('content')
<style>
    body {
        background: #1a1a2e !important;
    }
    
    .content-wrapper {
        background: #1a1a2e !important;
    }
    
    .dashboard-grid {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .grid-card {
        aspect-ratio: 1;
        border-radius: 20px;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .grid-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .grid-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .grid-card:hover::before {
        opacity: 1;
    }
    
    .card-icon {
        font-size: 56px;
        margin-bottom: auto;
        opacity: 0.95;
    }
    
    .card-content {
        margin-top: auto;
    }
    
    .card-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .card-subtitle {
        font-size: 13px;
        opacity: 0.9;
        font-weight: 400;
    }
    
    /* Couleurs spécifiques */
    .card-categories {
        background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
    }
    
    .card-articles {
        background: linear-gradient(135deg, #a8b5c0, #8a9ba8);
    }
    
    .card-mouvements {
        background: linear-gradient(135deg, #4ecdc4, #44a6c6);
    }
    
    .card-demandes {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }
    
    .card-entrees {
        background: linear-gradient(135deg, #2ecc71, #27ae60);
    }
    
    .card-sorties {
        background: linear-gradient(135deg, #ff8243, #e67e22);
    }
    
    .card-fournisseurs {
        background: linear-gradient(135deg, #3498db, #2980b9);
    }
    
    .card-commandes {
        background: linear-gradient(135deg, #f39c12, #e67e22);
    }
    
    .card-stats {
        background: linear-gradient(135deg, #16a085, #1abc9c);
    }
    
    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .grid-card {
            padding: 20px;
        }
        
        .card-icon {
            font-size: 42px;
        }
        
        .card-title {
            font-size: 20px;
        }
        
        .card-subtitle {
            font-size: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .dashboard-grid {
            padding: 15px;
        }
        
        .grid-container {
            gap: 12px;
        }
        
        .grid-card {
            padding: 18px;
        }
        
        .card-icon {
            font-size: 38px;
        }
        
        .card-title {
            font-size: 18px;
        }
    }
    
    /* Animation d'entrée */
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
    
    .grid-card {
        animation: fadeInUp 0.5s ease forwards;
    }
    
    .grid-card:nth-child(1) { animation-delay: 0.1s; }
    .grid-card:nth-child(2) { animation-delay: 0.2s; }
    .grid-card:nth-child(3) { animation-delay: 0.3s; }
    .grid-card:nth-child(4) { animation-delay: 0.4s; }
    .grid-card:nth-child(5) { animation-delay: 0.5s; }
    .grid-card:nth-child(6) { animation-delay: 0.6s; }
    .grid-card:nth-child(7) { animation-delay: 0.7s; }
    .grid-card:nth-child(8) { animation-delay: 0.8s; }
</style>

<div class="dashboard-grid">
    <div class="grid-container">
        
        {{-- Catégories (Stock) - Gestionnaire & Directeur avec permission manage stock --}}
        @can('manage stock')
        <a href="{{ route('categories.index') }}" class="grid-card card-categories">
            <div class="card-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Stock</div>
                <div class="card-subtitle">Gérer les catégories</div>
            </div>
        </a>
        @endcan


        {{-- Mouvements (Historique) - Gestionnaire & Directeur avec permission manage stock --}}
        @can('manage stock')
        <a href="{{ route('mouvements.index') }}" class="grid-card card-mouvements">
            <div class="card-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Historique</div>
                <div class="card-subtitle">Consulter les mouvements</div>
            </div>
        </a>
        @endcan

        {{-- Demandes - Employé (ses demandes) / Gestionnaire (toutes) --}}
        @role('Employé')
        <a href="{{ route('employe.demandes.index') }}" class="grid-card card-demandes">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Mes Demandes</div>
                <div class="card-subtitle">Consulter mes demandes</div>
            </div>
        </a>
        @endrole

        @role('Gestionnaire')
        <a href="{{ route('demandes.index') }}" class="grid-card card-demandes">
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Demandes</div>
                <div class="card-subtitle">Gérer toutes les demandes</div>
            </div>
        </a>
        @endrole

        {{-- Entrées - Gestionnaire & Directeur avec permission manage stock --}}
        @can('manage stock')
        <a href="{{ route('entrees.index') }}" class="grid-card card-entrees">
            <div class="card-icon">
                <i class="fas fa-cart-plus"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Entrées</div>
                <div class="card-subtitle">Consulter les entrées</div>
            </div>
        </a>
        @endcan

        {{-- Sorties - Gestionnaire & Directeur avec permission manage stock --}}
        @can('manage stock')
        <a href="{{ route('sorties.index') }}" class="grid-card card-sorties">
            <div class="card-icon">
                <i class="fas fa-cart-arrow-down"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Sorties</div>
                <div class="card-subtitle">Consulter les sorties</div>
            </div>
        </a>
        @endcan

        {{-- Fournisseurs - Gestionnaire & Directeur avec permission manage suppliers --}}
        @can('manage suppliers')
        <a href="{{ route('suppliers.index') }}" class="grid-card card-fournisseurs">
            <div class="card-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Fournisseurs</div>
                <div class="card-subtitle">Gérer les fournisseurs</div>
            </div>
        </a>
        @endcan

        {{-- Commandes - Gestionnaire & Directeur --}}
        @role('Gestionnaire|Directeur')
        <a href="{{ route('commandes.index') }}" class="grid-card card-commandes">
            <div class="card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Commandes</div>
                <div class="card-subtitle">Gérer les commandes</div>
            </div>
        </a>
        @endrole

        {{-- Statistiques - Gestionnaire & Directeur --}}
        @role('Gestionnaire|Directeur')
        <a href="#" class="grid-card card-stats">
            <div class="card-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="card-content">
                <div class="card-title">Statistiques</div>
                <div class="card-subtitle">Consulter les statistiques</div>
            </div>
        </a>
        @endrole

    </div>
</div>
@stop

@section('js')
<script>
    // Force le style après le chargement
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Sidebar styles loaded');
    });
</script>
@stop