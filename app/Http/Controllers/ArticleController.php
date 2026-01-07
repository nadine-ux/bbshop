<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Fournisseur;
use App\Models\Category;
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
            return view('articles.create', compact('fournisseurs','categories'));
        }
    /**
     * Enregistrer un nouvel article
     */
   public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'code_barres' => 'required|unique:articles',
        'quantite_minimale' => 'required|integer|min:0',
        'prix_achat' => 'nullable|numeric',
        'fournisseur_id' => 'nullable|exists:fournisseurs,id',
        'categorie_id' => 'required|exists:categories,id', 
        'stock' => 'required|integer|min:0',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    // on prend tout sauf le champ photo
    $data = $request->except('photo');

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('articles', 'public');
        $data['photo'] = '/storage/'.$path;
    }

    Article::create($data);

    return redirect()->route('articles.index')->with('success','Article créé avec succès');
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

}
