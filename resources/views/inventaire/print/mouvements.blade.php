<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal des Mouvements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
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
        }
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #f5f5f5;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-box strong {
            display: block;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            color: white;
        }
        .badge-entree { background-color: #28a745; }
        .badge-sortie { background-color: #dc3545; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 JOURNAL DES MOUVEMENTS</h1>
        <p>{{ $stats['periode'] }}</p>
        <p>Imprimé le {{ $stats['date_impression'] }}</p>
    </div>

    <div class="stats-bar">
        <div class="stat-box">
            <strong>{{ $stats['total_mouvements'] }}</strong>
            <span>Total Mouvements</span>
        </div>
        <div class="stat-box">
            <strong style="color: #28a745;">{{ $stats['total_entrees'] }}</strong>
            <span>Total Entrées</span>
        </div>
        <div class="stat-box">
            <strong style="color: #dc3545;">{{ $stats['total_sorties'] }}</strong>
            <span>Total Sorties</span>
        </div>
        <div class="stat-box">
            <strong>{{ $stats['total_entrees'] - $stats['total_sorties'] }}</strong>
            <span>Différence</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Article</th>
                <th>Type</th>
                <th>Qté</th>
                <th>Avant</th>
                <th>Après</th>
                <th>Motif</th>
                <th>Utilisateur</th>
                <th>Réf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mouvements as $mouvement)
            <tr>
                <td>{{ $mouvement->date_mouvement->format('d/m H:i') }}</td>
                <td>{{ $mouvement->article->nom }}</td>
                <td>
                    <span class="badge badge-{{ $mouvement->type }}">
                        {{ $mouvement->type == 'entree' ? '📥' : '📤' }}
                    </span>
                </td>
                <td>
                    <strong class="{{ $mouvement->type == 'entree' ? 'text-success' : 'text-danger' }}">
                        {{ $mouvement->type == 'entree' ? '+' : '-' }}{{ $mouvement->quantite }}
                    </strong>
                </td>
                <td>{{ $mouvement->stock_avant }}</td>
                <td><strong>{{ $mouvement->stock_apres }}</strong></td>
                <td>{{ \Str::limit($mouvement->motif ?? '-', 20) }}</td>
                <td>{{ $mouvement->user->name ?? '-' }}</td>
                <td>
                    @if($mouvement->entree_id)
                        E#{{ $mouvement->entree_id }}
                    @elseif($mouvement->sortie_id)
                        S#{{ $mouvement->sortie_id }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion d'inventaire</p>
    </div>
</body>
</html>