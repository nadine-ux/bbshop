<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventaire Global</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .info-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }
        .info-item {
            text-align: center;
        }
        .info-item strong {
            display: block;
            font-size: 18px;
            color: #2c3e50;
        }
        .info-item span {
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stock-critique {
            background-color: #fff3cd !important;
        }
        .stock-epuise {
            background-color: #f8d7da !important;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>📦 INVENTAIRE GLOBAL</h1>
        <p>Imprimé le {{ $stats['date_impression'] }}</p>
    </div>

    <!-- Statistiques -->
    <div class="info-bar">
        <div class="info-item">
            <strong>{{ $stats['total_articles'] }}</strong>
            <span>Articles</span>
        </div>
        <div class="info-item">
            <strong>{{ $stats['stock_critique'] }}</strong>
            <span>Stock Critique</span>
        </div>
        <div class="info-item">
            <strong>{{ number_format($stats['valeur_stock'], 2) }} DA</strong>
            <span>Valeur Totale</span>
        </div>
        <div class="info-item">
            <strong>{{ $stats['articles_epuises'] }}</strong>
            <span>Épuisés</span>
        </div>
    </div>

    <!-- Tableau -->
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Article</th>
                <th>Catégorie</th>
                <th>Fournisseur</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Min</th>
                <th class="text-right">Prix Achat</th>
                <th class="text-right">Valeur</th>
                <th class="text-center">État</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr class="{{ $article->stock == 0 ? 'stock-epuise' : ($article->stock_critique ? 'stock-critique' : '') }}">
                <td>{{ $article->code_barres ?? '-' }}</td>
                <td><strong>{{ $article->nom }}</strong></td>
                <td>{{ $article->category->nom ?? '-' }}</td>
                <td>{{ $article->fournisseur->nom ?? '-' }}</td>
                <td class="text-center"><strong>{{ $article->stock }}</strong></td>
                <td class="text-center">{{ $article->quantite_minimale }}</td>
                <td class="text-right">{{ number_format($article->prix_achat, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($article->stock * $article->prix_achat, 2) }}</strong></td>
                <td class="text-center">
                    @if($article->stock == 0)
                        <span class="badge badge-danger">ÉPUISÉ</span>
                    @elseif($article->stock_critique)
                        <span class="badge badge-warning">CRITIQUE</span>
                    @else
                        <span class="badge badge-success">OK</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré automatiquement par le système de gestion d'inventaire</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>