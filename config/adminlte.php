<?php



return [

    'title' => 'Gestion de Stock',

    'logo' => '<b>BB Shopping</b>',
    'logo_img' => 'vendor/adminlte/dist/img/AdminLTELogo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Adredje Logo',

   'menu' => [
    // Tableau de bord
    [
        'text' => 'Dashboard',
        'url'  => '/dashboard',
        'icon' => 'fas fa-home',
        'can'  => 'view dashboard',
    ],
// Gestion des catégories
['header' => 'CATÉGORIES'],
[
    'text' => 'Catégories',
    'url'  => 'categories',
    'icon' => 'fas fa-tags',
    'can'  => 'manage stock',
],
[
    'text' => 'Sous‑catégories',
    'url'  => 'subcategories',
    'icon' => 'fas fa-tag',
    'can'  => 'manage stock',
],

    // Gestion des articles
    ['header' => 'GESTION DES ARTICLES'],
    [
        'text' => 'Articles',
        'url'  => 'articles',
        'icon' => 'fas fa-box',
        'can'  => 'manage stock',
    ],

    // Gestion des fournisseurs
    ['header' => 'FOURNISSEURS'],
    [
        'text' => 'Fournisseurs',
        'url'  => 'suppliers',
        'icon' => 'fas fa-truck',
        'can'  => 'manage suppliers',
    ],

    // Gestion du stock
    ['header' => 'GESTION DU STOCK'],
    [
        'text' => 'Entrées',
        'url'  => 'entrees',
        'icon' => 'fas fa-arrow-down',
        'can'  => 'manage stock',
    ],
    [
        'text' => 'Sorties',
        'url'  => 'sorties',
        'icon' => 'fas fa-arrow-up',
        'can'  => 'manage stock',
    ],
    [
        'text' => 'Mouvements',
        'url'  => 'mouvements',
        'icon' => 'fas fa-exchange-alt',
        'can'  => 'manage stock',
    ],

    // Demandes internes
    ['header' => 'DEMANDES INTERNES'],
    [
        'text' => 'Mes demandes',
        'url'  => 'my-requests',
        'icon' => 'fas fa-envelope-open-text',
        'can'  => 'view own requests',
    ],
    [
    'text' => 'Demandes à traiter',
    'url'  => 'requests',
    'icon' => 'fas fa-tasks',
    'can'  => 'manage requests', 
],
['header' => 'COMMANDES'],
[
    'text' => 'Commandes',
    'url'  => 'commandes',
    'icon' => 'fas fa-shopping-cart',
    'can'  => 'manage commandes',
],


    // Utilisateurs
    ['header' => 'UTILISATEURS'],
    [
        'text' => 'Employés',
        'url'  => 'employees',
        'icon' => 'fas fa-user-friends',
        'can'  => 'manage users',
    ],
    [
        'text' => 'Gestion des rôles',
        'url'  => 'roles',
        'icon' => 'fas fa-user-shield',
        'can'  => 'manage roles',
    ],

    // Rapports
    ['header' => 'RAPPORTS & HISTORIQUE'],
    [
        'text' => 'Rapports',
        'url'  => 'admin/reports',
        'icon' => 'fas fa-file-alt',
        'can'  => 'view reports',
    ],
    [
        'text' => 'Journal d’audit',
        'url'  => 'audit',
        'icon' => 'fas fa-history',
        'can'  => 'view audit',
    ],
],

];

