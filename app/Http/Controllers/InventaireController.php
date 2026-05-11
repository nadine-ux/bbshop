<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Inventaire;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventaireController extends Controller
{
    /**
     * 📊 Vue générale de l'inventaire
     */
    public function index(Request $request)
{
    $query = Article::with(['category', 'fournisseur']);

    if ($request->filled('categorie_id')) {
        $query->where('categorie_id', $request->categorie_id);
    }

    if ($request->filled('stock_critique')) {
        $query->whereRaw('stock <= quantite_minimale');
    }

    if ($request->filled('recherche')) {
        $search = $request->recherche;
        $query->where(function($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('code_barres', 'like', "%{$search}%");
        });
    }

    $articles = $query->paginate(20);
    
    $categories = Category::whereNull('parent_id')->orderBy('nom')->get();

    $stats = [
        'total_articles' => Article::count(),
        'stock_critique' => Article::whereRaw('stock <= quantite_minimale')->count(),
        'valeur_stock' => Article::sum(DB::raw('stock * prix_achat')),
        'articles_epuises' => Article::where('stock', 0)->count(),
    ];

    return view('inventaire.index', compact('articles', 'categories', 'stats'));
}

    /**
     * 📦 Détails d'un article avec historique depuis la table inventaires
     */
    public function show(Article $article)
    {
        $article->load(['category', 'fournisseur']);

        // 📜 Historique complet depuis la table inventaires
        $mouvements = Inventaire::where('article_id', $article->id)
            ->with(['user', 'entree.fournisseur', 'sortie'])
            ->orderBy('date_mouvement', 'desc')
            ->paginate(20);

        // 📊 Statistiques de l'article
        $stats = [
            'total_entrees' => Inventaire::where('article_id', $article->id)
                ->where('type', 'entree')
                ->sum('quantite'),
            'total_sorties' => Inventaire::where('article_id', $article->id)
                ->where('type', 'sortie')
                ->sum('quantite'),
            'valeur_stock' => $article->stock * $article->prix_achat,
            'stock_critique' => $article->stock_critique,
            'nombre_mouvements' => Inventaire::where('article_id', $article->id)->count(),
        ];

        // 📥 Dernières entrées (depuis la table pivot pour affichage détaillé)
        $entrees = $article->entrees()
            ->with('fournisseur')
            ->orderBy('date_reception', 'desc')
            ->paginate(10, ['*'], 'entrees_page');

        // 📤 Dernières sorties
        $sorties = $article->sorties()
            ->orderBy('date_sortie', 'desc')
            ->paginate(10, ['*'], 'sorties_page');

        return view('inventaire.show', compact('article', 'mouvements', 'entrees', 'sorties', 'stats'));
    }

    /**
     * 📊 Rapport des mouvements de stock (depuis table inventaires)
     */
    public function mouvements(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth());
        $dateFin = $request->input('date_fin', now()->endOfMonth());

        // 📜 Tous les mouvements de la période
        $mouvements = Inventaire::with(['article', 'user', 'entree.fournisseur', 'sortie'])
            ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
            ->orderBy('date_mouvement', 'desc')
            ->paginate(30);

        // 📊 Statistiques de la période
        $stats = [
            'total_mouvements' => Inventaire::whereBetween('date_mouvement', [$dateDebut, $dateFin])->count(),
            'total_entrees' => Inventaire::whereBetween('date_mouvement', [$dateDebut, $dateFin])
                ->where('type', 'entree')
                ->sum('quantite'),
            'total_sorties' => Inventaire::whereBetween('date_mouvement', [$dateDebut, $dateFin])
                ->where('type', 'sortie')
                ->sum('quantite'),
            'nombre_articles' => Inventaire::whereBetween('date_mouvement', [$dateDebut, $dateFin])
                ->distinct('article_id')
                ->count('article_id'),
        ];

        return view('inventaire.mouvements', compact('mouvements', 'dateDebut', 'dateFin', 'stats'));
    }

    /**
     * ⚠️ Articles en stock critique
     */
    public function stockCritique()
    {
        $articles = Article::with(['category', 'fournisseur'])
            ->whereRaw('stock <= quantite_minimale')
            ->orderBy('stock', 'asc')
            ->paginate(20);

        return view('inventaire.stock-critique', compact('articles'));
    }

    /**
     * 💰 Valorisation du stock
     */
    public function valorisation(Request $request)
    {
        $query = Article::with('category');

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        $articles = $query->get()->map(function($article) {
            return [
                'article' => $article,
                'valeur' => $article->stock * $article->prix_achat,
                'stock' => $article->stock,
                'prix_achat' => $article->prix_achat,
            ];
        })->sortByDesc('valeur');

        $categories = Category::all();
        $valeurTotale = $articles->sum('valeur');

        // 📊 Top 10 des articles les plus valorisés
        $topArticles = $articles->take(10);

        return view('inventaire.valorisation', compact('articles', 'categories', 'valeurTotale', 'topArticles'));
    }

    /**
     * 📈 Graphique d'évolution du stock d'un article
     */
    public function evolution(Article $article, Request $request)
    {
        $jours = $request->input('jours', 30); // 30 derniers jours par défaut

        $evolution = Inventaire::where('article_id', $article->id)
            ->where('date_mouvement', '>=', now()->subDays($jours))
            ->orderBy('date_mouvement', 'asc')
            ->get()
            ->map(function($mouvement) {
                return [
                    'date' => $mouvement->date_mouvement->format('d/m/Y H:i'),
                    'stock' => $mouvement->stock_apres,
                    'type' => $mouvement->type,
                    'quantite' => $mouvement->quantite,
                ];
            });

        return response()->json($evolution);
    }

    /**
     * 📄 Export Excel/PDF (optionnel - à implémenter avec Laravel Excel)
     */
    public function export(Request $request)
    {
        // TODO: Implémenter l'export avec Laravel Excel ou DomPDF
        return back()->with('info', 'Fonctionnalité d\'export à venir');
    }

    /**
     * 🖨️ Imprimer l'inventaire complet
     */
    public function print(Request $request)
    {
        $query = Article::with(['category', 'fournisseur']);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('stock_critique')) {
            $query->whereRaw('stock <= quantite_minimale');
        }

        $articles = $query->get();

        // Statistiques
        $stats = [
            'total_articles' => $articles->count(),
            'stock_critique' => $articles->filter(function($a) {
                return $a->stock <= $a->quantite_minimale;
            })->count(),
            'valeur_stock' => $articles->sum(function($a) {
                return $a->stock * $a->prix_achat;
            }),
            'articles_epuises' => $articles->where('stock', 0)->count(),
            'date_impression' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('inventaire.print.inventaire', compact('articles', 'stats'))
            ->setPaper('a4', 'landscape'); // Format paysage

        return $pdf->stream('inventaire_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * 🖨️ Imprimer la fiche d'un article
     */
    public function printArticle(Article $article)
    {
        $article->load(['category', 'fournisseur']);

        // Historique complet
        $mouvements = Inventaire::where('article_id', $article->id)
            ->with(['user', 'entree.fournisseur', 'sortie'])
            ->orderBy('date_mouvement', 'desc')
            ->get();

        // Statistiques
        $stats = [
            'total_entrees' => Inventaire::where('article_id', $article->id)
                ->where('type', 'entree')
                ->sum('quantite'),
            'total_sorties' => Inventaire::where('article_id', $article->id)
                ->where('type', 'sortie')
                ->sum('quantite'),
            'valeur_stock' => $article->stock * $article->prix_achat,
            'date_impression' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('inventaire.print.article', compact('article', 'mouvements', 'stats'))
            ->setPaper('a4');

        return $pdf->stream('article_' . $article->id . '_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * 🖨️ Imprimer le stock critique
     */
    public function printStockCritique()
    {
        $articles = Article::with(['category', 'fournisseur'])
            ->whereRaw('stock <= quantite_minimale')
            ->orderBy('stock', 'asc')
            ->get();

        $stats = [
            'total_articles' => $articles->count(),
            'articles_epuises' => $articles->where('stock', 0)->count(),
            'date_impression' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('inventaire.print.stock-critique', compact('articles', 'stats'))
            ->setPaper('a4');

        return $pdf->stream('stock_critique_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * 🖨️ Imprimer les mouvements
     */
    public function printMouvements(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth());
        $dateFin = $request->input('date_fin', now()->endOfMonth());

        $mouvements = Inventaire::with(['article', 'user', 'entree.fournisseur', 'sortie'])
            ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
            ->orderBy('date_mouvement', 'desc')
            ->get();

        $stats = [
            'total_mouvements' => $mouvements->count(),
            'total_entrees' => $mouvements->where('type', 'entree')->sum('quantite'),
            'total_sorties' => $mouvements->where('type', 'sortie')->sum('quantite'),
            'periode' => 'Du ' . \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') . 
                        ' au ' . \Carbon\Carbon::parse($dateFin)->format('d/m/Y'),
            'date_impression' => now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('inventaire.print.mouvements', compact('mouvements', 'stats'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('mouvements_' . now()->format('Y-m-d') . '.pdf');
    }
  }
