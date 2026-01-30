<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Article - {{ $article->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .article-info {
            background: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-row {
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-box strong {
            display: block;
            font-size: 24px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .badge-entree { background-color: #28a745; }
        .badge-sortie { background-color: #dc3545; }
        .badge-ajustement { background-color: #ffc107; color: #000; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>📦 FICHE ARTICLE</h1>
        <h2>{{ $article->nom }}</h2>
        <p>Imprimé le {{ $stats['date_impression'] }}</p>
    </div>

    <!-- Informations article -->
    <div class="article-info">
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Code-barres:</span>
                {{ $article->code_barres ?? '-' }}
            </div>
            <div class="info-row">
                <span class="info-label">Catégorie:</span>
                {{ $article->category->nom ?? '-' }}
            </div>
            <div class="info-row">
                <span class="info-label">Fournisseur:</span>
                {{ $article->fournisseur->nom ?? '-' }}
            </div>
            <div class="info-row">
                <span class="info-label">Prix d'achat:</span>
                {{ number_format($article->prix_achat, 2) }} DA
            </div>
            <div class="info-row">
                <span class="info-label">Stock minimum:</span>
                {{ $article->quantite_minimale }}
            </div>
            <div class="info-row">
                <span class="info-label">Contenance carton:</span>
                {{ $article->contenance_carton }} pièces
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-bar">
        <div class="stat-box">
            <strong>{{ $article->stock }}</strong>
            <span>Stock Actuel</span>
        </div>
        <div class="stat-box">
            <strong>{{ $stats['total_entrees'] }}</strong>
            <span>Total Entrées</span>
        </div>
        <div class="stat-box">
            <strong>{{ $stats['total_sorties'] }}</strong>
            <span>Total Sorties</span>
        </div>
        <div class="stat-box">
            <strong>{{ number_format($stats['valeur_stock'], 2) }} DA</strong>
            <span>Valeur Stock</span>
        </div>
    </div>

    <!-- Historique des mouvements -->
    <h3>📜 Historique des mouvements</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Stock Avant</th>
                <th>Stock Après</th>
                <th>Motif</th>
                <th>Utilisateur</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mouvements as $mouvement)
            <tr>
                <td>{{ $mouvement->date_mouvement->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge badge-{{ $mouvement->type }}">
                        {{ $mouvement->type_libelle }}
                    </span>
                </td>
                <td>
                    <strong class="{{ $mouvement->type == 'entree' ? 'text-success' : 'text-danger' }}">
                        {{ $mouvement->type == 'entree' ? '+' : '-' }}{{ $mouvement->quantite }}
                    </strong>
                </td>
                <td>{{ $mouvement->stock_avant }}</td>
                <td><strong>{{ $mouvement->stock_apres }}</strong></td>
                <td>{{ $mouvement->motif ?? '-' }}</td>
                <td>{{ $mouvement->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #666;">
                    Aucun mouvement enregistré
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré automatiquement par le système de gestion d'inventaire</p>
    </div>
</body>
</html>