<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Critique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #dc3545;
        }
        .alert {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat-box {
            text-align: center;
            padding: 10px;
        }
        .stat-box strong {
            display: block;
            font-size: 20px;
            color: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .critique { background-color: #fff3cd; }
        .epuise { background-color: #f8d7da; }
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
    <div class="header">
        <h1>⚠️ ALERTE STOCK CRITIQUE</h1>
        <p>Imprimé le {{ $stats['date_impression'] }}</p>
    </div>

    @if($stats['total_articles'] > 0)
    <div class="alert">
        <strong>⚠️ ATTENTION !</strong> 
        {{ $stats['total_articles'] }} article(s) nécessitent un réapprovisionnement urgent
    </div>
    @endif

    <div class="stats">
        <div class="stat-box">
            <strong>{{ $stats['total_articles'] }}</strong>
            <span>Articles critiques</span>
        </div>
        <div class="stat-box">
            <strong>{{ $stats['articles_epuises'] }}</strong>
            <span>Articles épuisés</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Catégorie</th>
                <th>Fournisseur</th>
                <th>Stock Actuel</th>
                <th>Stock Min</th>
                <th>Écart</th>
                <th>Prix Achat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr class="{{ $article->stock == 0 ? 'epuise' : 'critique' }}">
                <td><strong>{{ $article->nom }}</strong></td>
                <td>{{ $article->category->nom ?? '-' }}</td>
                <td>{{ $article->fournisseur->nom ?? '-' }}</td>
                <td><strong>{{ $article->stock }}</strong></td>
                <td>{{ $article->quantite_minimale }}</td>
                <td><strong>{{ $article->stock - $article->quantite_minimale }}</strong></td>
                <td>{{ number_format($article->prix_achat, 2) }} DA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>⚠️ Document prioritaire - Action requise</p>
    </div>
</body>
</html>