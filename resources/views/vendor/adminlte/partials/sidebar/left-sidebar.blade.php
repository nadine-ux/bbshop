<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    <style>
        /* ============================================
           SIDEBAR BB SHOPPING - Gestion Superette
           ============================================ */

        /* Sidebar principale - ton foncé élégant */
        .main-sidebar {
            background: linear-gradient(180deg, #1e1e2e 0%, #2a2a3e 100%) !important;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2) !important;
            border-right: 1px solid rgba(255, 107, 53, 0.1) !important;
        }

        /* Logo/Brand - Couleurs BB Shopping (orange/rouge) */
        .brand-link {
            background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
            padding: 1.25rem 1.5rem !important;
            transition: all 0.4s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        /* Effet de brillance sur le logo */
        .brand-link::after {
            content: '' !important;
            position: absolute !important;
            top: -50% !important;
            left: -50% !important;
            width: 200% !important;
            height: 200% !important;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent) !important;
            transform: rotate(45deg) !important;
            transition: all 0.6s ease !important;
        }

        .brand-link:hover::after {
            left: 100% !important;
        }

        .brand-link:hover {
            background:linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4) !important;
        }

        .brand-text {
            color: white !important;
            font-weight: 800 !important;
            font-size: 1.3rem !important;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
            letter-spacing: 0.5px !important;
        }

        .brand-image {
            opacity: 1 !important;
            filter: brightness(1.1) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) !important;
            transition: transform 0.3s ease !important;
        }

        .brand-link:hover .brand-image {
            transform: scale(1.05) rotate(-5deg) !important;
        }

        /* Sidebar menu */
        .sidebar {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        /* Headers de section - Style superette */
        .nav-header {
            color: #FF8C42 !important;
            font-size: 0.7rem !important;
            font-weight: 800 !important;
            letter-spacing: 1.5px !important;
            padding: 1.25rem 1.25rem 0.75rem 1.25rem !important;
            margin-top: 0.75rem !important;
            text-transform: uppercase !important;
            position: relative !important;
            border-bottom: 2px solid rgba(255, 140, 66, 0.2) !important;
            margin-bottom: 0.5rem !important;
        }

        .nav-header::before {
            content: '' !important;
            position: absolute !important;
            left: 1.25rem !important;
            bottom: -2px !important;
            width: 40px !important;
            height: 2px !important;
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
        }

        /* Items du menu - Design moderne */
        .nav-sidebar .nav-item {
            margin-bottom: 0.35rem !important;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            border-radius: 12px !important;
            margin: 0 0.75rem !important;
            padding: 0.9rem 1.25rem !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            backdrop-filter: blur(10px) !important;
            font-weight: 500 !important;
        }

        /* Bordure gauche animée - couleur BB Shopping */
        .nav-sidebar .nav-link::before {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            height: 0% !important;
            width: 4px !important;
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            border-radius: 0 4px 4px 0 !important;
            transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.5) !important;
        }

        .nav-sidebar .nav-link:hover::before {
            height: 70% !important;
        }

        /* Effet de fond au survol */
        .nav-sidebar .nav-link::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.15), rgba(255, 140, 66, 0.15)) !important;
            opacity: 0 !important;
            border-radius: 12px !important;
            transition: opacity 0.4s ease !important;
            z-index: -1 !important;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255, 140, 66, 0.1) !important;
            color: white !important;
            transform: translateX(6px) !important;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.2) !important;
        }

        .nav-sidebar .nav-link:hover::after {
            opacity: 1 !important;
        }

        /* Item actif - Couleur BB Shopping */
        .nav-sidebar .nav-link.active {
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            color: white !important;
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
            font-weight: 600 !important;
            transform: translateX(4px) !important;
        }

        .nav-sidebar .nav-link.active::before {
            height: 100% !important;
            width: 5px !important;
            background: linear-gradient(180deg, #fff, #FFE5D9) !important;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.6) !important;
        }

        /* Icônes du menu - Animation fluide */
        .nav-sidebar .nav-icon {
            margin-right: 1rem !important;
            font-size: 1.15rem !important;
            width: 1.75rem !important;
            text-align: center !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2)) !important;
        }

        .nav-sidebar .nav-link:hover .nav-icon {
            transform: scale(1.15) rotate(-5deg) !important;
            filter: drop-shadow(0 4px 8px rgba(255, 107, 53, 0.4)) !important;
        }

        .nav-sidebar .nav-link.active .nav-icon {
            color: white !important;
            transform: scale(1.1) !important;
            filter: drop-shadow(0 2px 8px rgba(255, 255, 255, 0.5)) !important;
        }

        /* Badge/Compteur sur les items */
        .nav-sidebar .badge {
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3) !important;
            font-weight: 700 !important;
            padding: 0.3rem 0.6rem !important;
        }

        /* Scrollbar personnalisée - Couleur BB Shopping */
        .sidebar::-webkit-scrollbar {
            width: 8px !important;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2) !important;
            border-radius: 10px !important;
            margin: 10px 0 !important;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;
            border-radius: 10px !important;
            border: 2px solid rgba(30, 30, 46, 0.5) !important;
            transition: all 0.3s ease !important;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%) !important;

            box-shadow: 0 0 10px rgba(255, 107, 53, 0.5) !important;
        }

        /* Animation de chargement des items */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .nav-sidebar .nav-item {
            animation: slideInLeft 0.5s ease forwards;
            animation-delay: calc(var(--item-index) * 0.05s);
        }

        /* Mode sidebar réduite */
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            box-shadow: 6px 0 30px rgba(255, 107, 53, 0.2) !important;
        }

        /* Sous-menu (si vous en avez) */
        .nav-treeview {
            padding-left: 0 !important;
            background: rgba(0, 0, 0, 0.1) !important;
            border-radius: 0 0 12px 12px !important;
            margin: 0 0.75rem !important;
        }

        .nav-treeview .nav-link {
            padding-left: 3.5rem !important;
            font-size: 0.9rem !important;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .main-sidebar {
                box-shadow: none !important;
            }
            
            .nav-sidebar .nav-link {
                margin: 0 0.5rem !important;
            }
        }
    </style>

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                    data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
                @endif
                @if(!config('adminlte.sidebar_nav_accordion'))
                    data-accordion="false"
                @endif>
                {{-- Configured sidebar links --}}
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')
            </ul>
        </nav>
    </div>

</aside>