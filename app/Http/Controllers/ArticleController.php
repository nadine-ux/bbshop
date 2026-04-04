<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\Category;
use App\Models\Marque;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        // Seuls Directeur et Gestionnaire peuvent gérer le stock
        $this->middleware('permission:manage stock')->except(['index','show']);
    }

    /**
     * Liste des articles
     */
   public function index(Request $request)
{
    // Filters
    $nom           = $request->get('nom');
    $codeBarres    = $request->get('code_barres');
    $fournisseurId = $request->get('fournisseur_id');
    $categorieId   = $request->get('categorie_id');   // parent category
    $souscatId     = $request->get('souscategorie_id'); // child category
    $stockMin      = $request->get('stock_min');
    $stockMax      = $request->get('stock_max');
    $prixMin       = $request->get('prix_min');
    $prixMax       = $request->get('prix_max');

    $query = Article::with(['fournisseur', 'category.parent']);

    // Text filters
    if ($nom) {
        $query->where('nom', 'like', "%{$nom}%");
    }
    if ($codeBarres) {
        $query->where('code_barres', 'like', "%{$codeBarres}%");
    }

    // Relation filters
    if ($fournisseurId) {
        $query->where('fournisseur_id', $fournisseurId);
    }
    if ($categorieId) {
        // filter by parent category
        $query->whereHas('category.parent', function ($q) use ($categorieId) {
            $q->where('id', $categorieId);
        });
    }
    if ($souscatId) {
        // filter by child category
        $query->whereHas('category', function ($q) use ($souscatId) {
            $q->where('id', $souscatId);
        });
    }

    // Numeric ranges
    if ($stockMin !== null && $stockMin !== '') {
        $query->where('stock', '>=', (int)$stockMin);
    }
    if ($stockMax !== null && $stockMax !== '') {
        $query->where('stock', '<=', (int)$stockMax);
    }
    if ($prixMin !== null && $prixMin !== '') {
        $query->where('prix_achat', '>=', (float)$prixMin);
    }
    if ($prixMax !== null && $prixMax !== '') {
        $query->where('prix_achat', '<=', (float)$prixMax);
    }

    // Order newest first and paginate
    $articles = $query
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends($request->query());

    // For filter selects (optional)
    $fournisseurs = \App\Models\Fournisseur::orderBy('nom')->get(['id','nom']);
    $categories   = \App\Models\Category::whereNull('parent_id')->orderBy('nom')->get(['id','nom']);
    $souscats     = \App\Models\Category::whereNotNull('parent_id')->orderBy('nom')->get(['id','nom','parent_id']);

    return view('articles.index', compact('articles','fournisseurs','categories','souscats'));
}

    /**
     * Formulaire de création
     */

        public function create()
        {
            $fournisseurs = Fournisseur::all();
            $categories = Category::with('children')->get(); 
            $marques = Marque::orderBy('nom')->get(['id', 'nom']);
           return view('articles.create', compact('fournisseurs', 'categories', 'marques'));
        }
    /**
     * Enregistrer un nouvel article
     */
public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'code_barres' => 'required|string|unique:articles,code_barres',
        'categorie_id' => 'required|exists:categories,id',
        'marque_id' => 'nullable|exists:marques,id',
        'description' => 'nullable|string',
        'stock' => 'required|integer|min:0',
        'quantite_minimale' => 'required|integer|min:0',
        'contenance_carton' => 'nullable|integer|min:1',
        'prix_achat' => 'nullable|numeric|min:0',
        'prix_vente' => 'nullable|numeric|min:0',
        'date_peremption' => 'nullable|date',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    try {
        $data = $request->except('photo');
        
        // ✅ MÊME MÉTHODE que pour les catégories
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('articles', 'public');
        }

        // Créer l'article
        $article = Article::create($data);

        return redirect()->route('articles.index')
            ->with('success', 'Article créé avec succès !');

    } catch (\Exception $e) {
        // Si erreur, supprimer la photo uploadée
        if (isset($data['photo']) && \Storage::disk('public')->exists($data['photo'])) {
            \Storage::disk('public')->delete($data['photo']);
        }
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Erreur lors de la création : ' . $e->getMessage());
    }
}
public function getDetails($id)
{
    try {
        // Charger l'article avec les relations
        $article = Article::with(['category'])->findOrFail($id);
        
        // Récupérer l'historique des mouvements
        $mouvements = collect();
        
        // Vérifier si les relations existent avant de les utiliser
        if (method_exists($article, 'entrees')) {
            $entrees = \DB::table('article_entree')
                ->join('entrees', 'article_entree.entree_id', '=', 'entrees.id')
                ->leftJoin('fournisseurs', 'entrees.fournisseur_id', '=', 'fournisseurs.id')
                ->where('article_entree.article_id', $article->id)
                ->select(
                    'entrees.date_reception as date',
                    'article_entree.quantite_total as quantite',
                    'fournisseurs.nom as fournisseur_nom'
                )
                ->get();
            
            foreach($entrees as $entree) {
                $quantite = $entree->quantite ?? 0;
                $cartons = $article->contenance_carton > 0 
                    ? intdiv($quantite, $article->contenance_carton) 
                    : 0;
                $reste = $article->contenance_carton > 0 
                    ? $quantite % $article->contenance_carton 
                    : $quantite;
                
                $mouvements->push([
                    'date' => $entree->date,
                    'type' => 'Entrée',
                    'partenaire' => $entree->fournisseur_nom ?? 'N/A',
                    'quantite' => $quantite,
                    'detail' => "$cartons cartons, $reste pièces",
                ]);
            }
        }
        
        // Récupérer les sorties
        if (method_exists($article, 'sorties')) {
            $sorties = \DB::table('article_sortie')
                ->join('sorties', 'article_sortie.sortie_id', '=', 'sorties.id')
                ->where('article_sortie.article_id', $article->id)
                ->select(
                    'sorties.created_at as date',
                    'article_sortie.quantite_total as quantite',
                    'sorties.destination as destination'
                )
                ->get();
            
            foreach($sorties as $sortie) {
                $quantite = $sortie->quantite ?? 0;
                $cartons = $article->contenance_carton > 0 
                    ? intdiv($quantite, $article->contenance_carton) 
                    : 0;
                $reste = $article->contenance_carton > 0 
                    ? $quantite % $article->contenance_carton 
                    : $quantite;
                
                $mouvements->push([
                    'date' => $sortie->date,
                    'type' => 'Sortie',
                    'partenaire' => $sortie->destination ?? 'N/A',
                    'quantite' => $quantite,
                    'detail' => "$cartons cartons, $reste pièces",
                ]);
            }
        }
        
        // Trier par date décroissante
        $mouvements = $mouvements->sortByDesc('date')->take(10)->values();
        
        return view('articles.details-modal', compact('article', 'mouvements'));
        
    } catch (\Exception $e) {
        \Log::error('Erreur lors du chargement des détails de l\'article: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Erreur lors du chargement des détails',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Afficher un article
     */
     public function show(Article $article)
{
    return view('articles.show', compact('article'));
}

    /**
     * Formulaire d’édition
     */
    public function edit(Article $article)
    {
        $fournisseurs = Fournisseur::all();
        return view('articles.edit', compact('article','fournisseurs'));
    }

    /**
     * Mettre à jour un article
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'quantite_minimale' => 'required|integer|min:0',
        ]);

        $article->update($request->all());
        return redirect()->route('articles.index')->with('success','Article mis à jour');
    }

    /**
     * Supprimer un article
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success','Article supprimé');
    }
   // ─────────────────────────────────────────────────────────
    public function detailJson(Article $article)
    {
        $article->load(['category', 'marque', 'fournisseur']);

        return response()->json($article);
    }

    // ─────────────────────────────────────────────────────────
    //  GET /articles/{article}/mouvements-json
    //  → utilisé par le popup "Historique" (icône horloge)
    //  → lit la table inventaires via la relation inventaires()
    // ─────────────────────────────────────────────────────────
    public function mouvementsJson(Article $article)
    {
        // La relation inventaires() est dans Article.php :
        // public function inventaires() {
        //     return $this->hasMany(Inventaire::class)->orderBy('date_mouvement','desc');
        // }

        $mouvements = $article->inventaires()
            ->with(['entree.fournisseur', 'sortie'])   // charge les relations si elles existent dans Inventaire
            ->get()
            ->map(function ($inv) {

                // ── Construire le motif lisible ──────────────
                $motif = $inv->motif ?? null;

                // Depuis l'entrée liée
                if (!$motif && isset($inv->entree_id) && $inv->entree) {
                    $fournisseurNom = $inv->entree->fournisseur->nom ?? '';
                    $motif = 'Entrée fournisseur' . ($fournisseurNom ? " : {$fournisseurNom}" : '');
                    if ($inv->entree->commentaire) {
                        $motif .= ' — ' . $inv->entree->commentaire;
                    }
                }

                // Depuis la sortie liée
                if (!$motif && isset($inv->sortie_id) && $inv->sortie) {
                    $dest = $inv->sortie->destination ?? ($inv->sortie->motif ?? '');
                    $motif = 'Sortie' . ($dest ? " : {$dest}" : '');
                }

                // Fallback sur commentaire
                $motif = $motif ?: ($inv->commentaire ?? '—');

                // ── Date formatée ───────────────────────────
                $date = '—';
                if ($inv->date_mouvement) {
                    $date = \Carbon\Carbon::parse($inv->date_mouvement)->format('d/m/Y');
                } elseif ($inv->created_at) {
                    $date = $inv->created_at->format('d/m/Y');
                }

                return [
                    'type'     => $inv->type,               // 'entree' | 'sortie'
                    'quantite' => $inv->quantite,
                    'cartons'  => $inv->quantite_cartons ?? null,
                    'pieces'   => $inv->quantite_pieces  ?? null,
                    'motif'    => $motif,
                    'date'     => $date,
                ];
            });

        return response()->json($mouvements);
    }
}
