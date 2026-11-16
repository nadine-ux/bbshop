@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<style>
    /* ============================================
       NAVBAR BB SHOPPING - Gestion Superette
       ============================================ */

    /* Navbar moderne - Couleurs BB Shopping */
    .main-header.navbar {
   background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;

        border-bottom: none !important;
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.25) !important;
        padding: 0.75rem 1.5rem !important;
    }

    /* Links de la navbar */
    .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.95) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-radius: 10px !important;
        padding: 0.6rem 0.9rem !important;
        margin: 0 0.3rem !important;
        font-weight: 500 !important;
        position: relative !important;
    }

    .navbar-nav .nav-link::before {
        content: '' !important;
        position: absolute !important;
        bottom: 0 !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 0 !important;
        height: 2px !important;
        background: white !important;
        transition: width 0.3s ease !important;
    }

    .navbar-nav .nav-link:hover::before {
        width: 80% !important;
    }

    .navbar-nav .nav-link:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .navbar-nav .nav-link i {
        font-size: 1.3rem !important;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1)) !important;
    }

    /* Badges de notification modernes - BB Shopping Style */
    .navbar-badge {
        padding: 0.3rem 0.55rem !important;
        font-size: 0.65rem !important;
        font-weight: 800 !important;
        border-radius: 12px !important;
        position: absolute !important;
        top: 0.2rem !important;
        right: 0.2rem !important;
        min-width: 20px !important;
        height: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25) !important;
        border: 2px solid rgba(255, 255, 255, 0.3) !important;
    }

    .badge-warning {
       linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
        animation: pulse-warning 2s infinite !important;
    }

    .badge-danger {
        background: linear-gradient(135deg, #EF5350, #E53935) !important;
        animation: pulse-danger 2s infinite !important;
    }

    .badge-info {
        background: linear-gradient(135deg, #42A5F5, #1E88E5) !important;
        animation: pulse-info 2s infinite !important;
    }

    /* Animations pulse pour chaque type */
    @keyframes pulse-warning {
        0%, 100% {
            box-shadow: 0 3px 10px rgba(255, 167, 38, 0.4);
        }
        50% {
            box-shadow: 0 3px 20px rgba(255, 167, 38, 0.8), 0 0 0 4px rgba(255, 167, 38, 0.2);
        }
    }

    @keyframes pulse-danger {
        0%, 100% {
            box-shadow: 0 3px 10px rgba(239, 83, 80, 0.4);
        }
        50% {
            box-shadow: 0 3px 20px rgba(239, 83, 80, 0.8), 0 0 0 4px rgba(239, 83, 80, 0.2);
        }
    }

    @keyframes pulse-info {
        0%, 100% {
            box-shadow: 0 3px 10px rgba(66, 165, 245, 0.4);
        }
        50% {
            box-shadow: 0 3px 20px rgba(66, 165, 245, 0.8), 0 0 0 4px rgba(66, 165, 245, 0.2);
        }
    }

    /* Dropdown moderne */
    .dropdown-menu {
        border: none !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        padding: 0 !important;
        margin-top: 0.75rem !important;
        min-width: 340px !important;
        max-width: 400px !important;
        overflow: hidden !important;
    }

    .dropdown-menu-lg {
        max-height: 450px !important;
        overflow-y: auto !important;
    }

    /* Scrollbar personnalisée - Couleurs BB Shopping */
    .dropdown-menu-lg::-webkit-scrollbar {
        width: 8px !important;
    }

    .dropdown-menu-lg::-webkit-scrollbar-track {
        background: #f8f9fa !important;
        border-radius: 10px !important;
    }

    .dropdown-menu-lg::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
        border-radius: 10px !important;
        border: 2px solid #f8f9fa !important;
    }

    .dropdown-menu-lg::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
    }

    /* Header du dropdown - Style BB Shopping */
    .dropdown-header {
        padding: 1rem 1.5rem !important;
        font-weight: 800 !important;
        font-size: 0.9rem !important;
        color: white !important;
        background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
        border-bottom: none !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        box-shadow: 0 2px 8px rgba(255, 107, 53, 0.2) !important;
    }

    .dropdown-header i {
        color: white !important;
        margin-right: 0.5rem !important;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2)) !important;
    }

    /* Items du dropdown */
    .dropdown-item {
        padding: 1.25rem 1.5rem !important;
        border-left: 4px solid transparent !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        font-size: 0.9rem !important;
        border-bottom: 1px solid #f0f0f0 !important;
        background: white !important;
    }

    .dropdown-item:last-child {
        border-bottom: none !important;
    }

    .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(255, 107, 53, 0.08) 0%, rgba(255, 255, 255, 1) 100%) !important;
        transform: translateX(6px) !important;
    }

    .dropdown-item i {
        margin-right: 1rem !important;
        font-size: 1.2rem !important;
        width: 24px !important;
        text-align: center !important;
        transition: transform 0.3s ease !important;
    }

    .dropdown-item:hover i {
        transform: scale(1.15) !important;
    }

    /* Couleurs des icônes selon le type */
    .notif-demande {
        border-left-color: #FFA726 !important;
    }

    .notif-demande i {
        color: #FFA726 !important;
    }

    .notif-demande:hover {
        border-left-color: #FF8C42 !important;
    }

    .notif-stock {
        border-left-color: #EF5350 !important;
    }

    .notif-stock i {
        color: #EF5350 !important;
    }

    .notif-stock:hover {
        border-left-color: #E53935 !important;
    }

    .notif-commande {
        border-left-color: #42A5F5 !important;
    }

    .notif-commande i {
        color: #42A5F5 !important;
    }

    .notif-commande:hover {
        border-left-color: #1E88E5 !important;
    }

    .dropdown-item strong {
        color: #2c3e50 !important;
        font-weight: 700 !important;
    }

    .dropdown-item small {
        color: #7f8c8d !important;
        font-size: 0.85rem !important;
    }

    .dropdown-item .text-muted {
        color: #95a5a6 !important;
        font-size: 0.75rem !important;
        display: block !important;
        margin-top: 0.5rem !important;
        font-style: italic !important;
    }

    /* Message vide */
    .dropdown-item-empty {
        padding: 3rem 1.5rem !important;
        text-align: center !important;
        color: #bdc3c7 !important;
        font-style: italic !important;
        background: #f8f9fa !important;
    }

    .dropdown-item-empty i {
        display: block !important;
        margin: 0 auto 1rem auto !important;
        font-size: 3rem !important;
        opacity: 0.3 !important;
    }

    /* Animation pour le dropdown */
    .dropdown-menu {
        animation: slideDownFade 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-15px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* User menu dropdown */
    .navbar-nav .dropdown-menu {
        border-top: 3px solid #FF6B35 !important;
    }

    /* Sidebar toggler button */
    .navbar-nav .nav-link[data-widget="pushmenu"] {
        background: rgba(255, 255, 255, 0.15) !important;
    }

    .navbar-nav .nav-link[data-widget="pushmenu"]:hover {
        background: rgba(255, 255, 255, 0.25) !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-header.navbar {
            padding: 0.5rem 1rem !important;
        }

        .dropdown-menu {
            min-width: 300px !important;
            max-width: 90vw !important;
        }

        .navbar-nav .nav-link {
            padding: 0.5rem 0.7rem !important;
            margin: 0 0.2rem !important;
        }

        .dropdown-item {
            padding: 1rem 1.25rem !important;
        }
    }

    /* Mode sombre (si activé) */
    body.dark-mode .dropdown-item {
        background: #2c3e50 !important;
        color: #ecf0f1 !important;
    }

    body.dark-mode .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(255, 107, 53, 0.15) 0%, #34495e 100%) !important;
    }

    /* Effet de lumière sur la navbar */
    .main-header.navbar::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 2px !important;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.3) 25%, 
            rgba(255, 255, 255, 0.6) 50%, 
            rgba(255, 255, 255, 0.3) 75%, 
            transparent 100%
        ) !important;
    }
</style>

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        
        {{-- Notifications de demandes --}}
        @if(Auth::check())
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Demandes">
                <i class="far fa-bell"></i>
                @if(Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleDemandeNotification::class)->count() > 0)
                    <span class="badge badge-warning navbar-badge">
                        {{ Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleDemandeNotification::class)->count() }}
                    </span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">
                    <i class="fas fa-bell"></i>Nouvelles Demandes
                </span>
                @forelse(Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleDemandeNotification::class) as $notification)
                    <a href="{{ route('demandes.show', $notification->data['demande_id']) }}" class="dropdown-item notif-demande">
                        <i class="fas fa-user-circle"></i>
                        <div>
                            Demande de <strong>{{ $notification->data['employe'] }}</strong>
                            <br>
                            <small>{{ $notification->data['article'] }} ({{ $notification->data['quantite'] }} pièces)</small>
                            <span class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <span class="dropdown-item dropdown-item-empty">
                        <i class="far fa-bell-slash"></i>
                        Aucune notification de demande
                    </span>
                @endforelse
            </div>
        </li>
        @endif

        {{-- Notifications de stock minimal --}}
        @if(Auth::check())
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Alertes Stock">
                <i class="fas fa-exclamation-triangle"></i>
                @if(Auth::user()->unreadNotifications->where('type', App\Notifications\StockMinimalNotification::class)->count() > 0)
                    <span class="badge badge-danger navbar-badge">
                        {{ Auth::user()->unreadNotifications->where('type', App\Notifications\StockMinimalNotification::class)->count() }}
                    </span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">
                    <i class="fas fa-exclamation-triangle"></i>Alertes Stock
                </span>
                @forelse(Auth::user()->unreadNotifications->where('type', App\Notifications\StockMinimalNotification::class) as $notification)
                    <a href="{{ route('articles.show', $notification->data['article_id']) }}" class="dropdown-item notif-stock">
                        <i class="fas fa-box-open"></i>
                        <div>
                            {{ $notification->data['message'] }}
                            <span class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <span class="dropdown-item dropdown-item-empty">
                        <i class="fas fa-check-circle"></i>
                        Aucune alerte de stock
                    </span>
                @endforelse
            </div>
        </li>
        @endif

        {{-- Notifications de commandes --}}
        @if(Auth::check())
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Commandes">
                <i class="fas fa-shopping-cart"></i>
                @if(Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleCommandeNotification::class)->count() > 0)
                    <span class="badge badge-info navbar-badge">
                        {{ Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleCommandeNotification::class)->count() }}
                    </span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">
                    <i class="fas fa-shopping-cart"></i>Nouvelles Commandes
                </span>
                @forelse(Auth::user()->unreadNotifications->where('type', App\Notifications\NouvelleCommandeNotification::class) as $notification)
                    <a href="{{ route('commandes.show', $notification->data['commande_id']) }}" class="dropdown-item notif-commande">
                        <i class="fas fa-box"></i>
                        <div>
                            Commande par <strong>{{ $notification->data['user'] ?? 'N/A' }}</strong>
                            <br>
                            <small>{{ $notification->data['article'] ?? 'N/A' }} ({{ $notification->data['quantite'] ?? 'N/A' }} pièces)</small>
                            <span class="text-muted">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <span class="dropdown-item dropdown-item-empty">
                        <i class="fas fa-shopping-bag"></i>
                        Aucune commande
                    </span>
                @endforelse
            </div>
        </li>
        @endif

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>