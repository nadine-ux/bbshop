<?php

namespace App\Http\Controllers;

use App\Models\Sortie;
use App\Models\Article;
use Illuminate\Http\Request;

class SortieController extends Controller
{
    public function __construct()
    {
        // Seuls Directeur et Gestionnaire peuvent gérer les sorties
        $this->middleware('permission:manage stock');
    }

    /**
     * Liste des sorties
     */
    public function index()
    {
        $sorties = Sortie::with('articles')->paginate(10);
        return view('sorties.index', compact('sorties'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $articles = Article::all();
        return view('sorties.create', compact('articles'));
    }

    /**
     * Enregistrer une sortie
     */
public function store(Request $request)
{
    $request->validate([
        'destination' => 'nullable|string|max:255',
        'motif'       => 'nullable|string|max:255',
        'commentaire' => 'nullable|string',
        'articles'    => 'required|array',
        'articles.*.id' => 'exists:articles,id',
        'articles.*.cartons' => 'nullable|integer|min:0',
        'articles.*.pieces'  => 'nullable|integer|min:0',
    ]);

    $sortie = Sortie::create([
        'destination' => $request->destination,
        'motif'       => $request->motif,
        'commentaire' => $request->commentaire,
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

        // Mise à jour du stock (diminution)
        $article->stock -= $quantiteTotal;
        if ($article->stock < 0) {
            $article->stock = 0;
        }
        $article->save();

        // ✅ Vérification du seuil minimal
        if ($article->stock <= $article->quantite_minimale) {
            $users = \App\Models\User::role(['Gestionnaire', 'Directeur'])->get();
            \Illuminate\Support\Facades\Notification::send(
                $users,
                new \App\Notifications\StockMinimalNotification($article)
            );
        }

        // Insertion dans la pivot
        $sortie->articles()->attach($article->id, [
            'quantite_cartons' => $cartons,
            'quantite_pieces'  => $pieces,
            'quantite_total'   => $quantiteTotal,
        ]);
    }

    return redirect()->route('sorties.index')->with('success','Sortie enregistrée');
}

    /**
     * Afficher une sortie
     */
    public function show(Sortie $sortie)
    {$sortie->load('articles');

        return view('sorties.show', compact('sortie'));
    }

    /**
     * Formulaire d’édition
     */
    public function edit(Sortie $sortie)
    {
        $articles = Article::all();
        return view('sorties.edit', compact('sortie','articles'));
    }

    /**
     * Mettre à jour une sortie
     */
    public function update(Request $request, Sortie $sortie)
    {
        $request->validate([
            'date_sortie' => 'required|date',
        ]);

        $sortie->update($request->only('date_sortie','destination'));

        $sortie->articles()->detach();
        foreach ($request->articles as $article) {
            $sortie->articles()->attach($article['id'], [
                'quantite' => $article['quantite'],
            ]);
        }

        return redirect()->route('sorties.index')->with('success','Sortie mise à jour');
    }

    /**
     * Supprimer une sortie
     */
    public function destroy(Sortie $sortie)
    {
        $sortie->delete();
        return redirect()->route('sorties.index')->with('success','Sortie supprimée');
    }
}
