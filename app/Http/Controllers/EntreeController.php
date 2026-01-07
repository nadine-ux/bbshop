<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use App\Models\Fournisseur;
use App\Models\Article;
use Illuminate\Http\Request;

class EntreeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage stock');
    }

    /**
     * Liste des entrées
     */
    public function index()
    {
        $entrees = Entree::with('fournisseur','articles')->paginate(10);
        return view('entrees.index', compact('entrees'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $articles = Article::all();
        return view('entrees.create', compact('fournisseurs','articles'));
    }

    /**
     * Enregistrer une entrée
     */
public function store(Request $request)
{
    $request->validate([
        'date_reception' => 'required|date',
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'commentaire'    => 'nullable|string',
        'articles'       => 'required|array',
        'articles.*.id'  => 'exists:articles,id',
        'articles.*.cartons' => 'nullable|integer|min:0',
        'articles.*.pieces'  => 'nullable|integer|min:0',
    ]);

    $entree = Entree::create([
        'date_reception' => $request->date_reception,
        'fournisseur_id' => $request->fournisseur_id,
        'commentaire'    => $request->commentaire,
        'user_id'        => auth()->id(),
    ]);

    foreach ($request->articles as $articleData) {
        $article = Article::findOrFail($articleData['id']);

        $cartons = $articleData['cartons'] ?? 0;
        $pieces  = $articleData['pieces'] ?? 0;

        // ✅ Choisir cartons OU pièces
        if ($cartons > 0) {
            $quantiteTotal = $cartons * $article->contenance_carton;
        } else {
            $quantiteTotal = $pieces;
        }

        // Mise à jour du stock
        $article->stock += $quantiteTotal;
        $article->save();

        // Sauvegarde dans la pivot
        $entree->articles()->attach($article->id, [
            'quantite_cartons' => $cartons,
            'quantite_pieces'  => $pieces,
            'quantite_total'   => $quantiteTotal,
            'prix_unitaire'    => $articleData['prix_unitaire'] ?? null,
        ]);
    }

    return redirect()->route('entrees.index')->with('success','Entrée enregistrée');
}


    /**
     * Formulaire d’édition
     */
    public function edit(Entree $entree)
    {
        $fournisseurs = Fournisseur::all();
        $articles = Article::all();
        return view('entrees.edit', compact('entree','fournisseurs','articles'));
    }

    /**
     * Mettre à jour une entrée
     */
    public function update(Request $request, Entree $entree)
    {
        $request->validate([
            'date_reception' => 'required|date',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'numero_bon'     => 'required|string|max:255',
            'commentaire'    => 'nullable|string',
        ]);

        $entree->update($request->only('date_reception','fournisseur_id','numero_bon','commentaire'));

        $entree->articles()->detach();
        foreach ($request->articles as $article) {
            $entree->articles()->attach($article['id'], [
                'quantite' => $article['quantite'],
                'prix_unitaire' => $article['prix_unitaire'] ?? null,
            ]);
        }

        return redirect()->route('entrees.index')->with('success','Entrée mise à jour');
    }

    /**
     * Supprimer une entrée
     */
    public function destroy(Entree $entree)
    {
        $entree->delete();
        return redirect()->route('entrees.index')->with('success','Entrée supprimée');
    }
        public function show(Entree $entree)
    {
        $entree->load('fournisseur','articles'); // charger relations
        return view('entrees.show', compact('entree'));
    }

}
