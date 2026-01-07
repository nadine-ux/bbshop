<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Article;
 use App\Models\User;


use Illuminate\Http\Request;

class DemandeController extends Controller
{
    /**
     * Liste des demandes de l’employé connecté
     */
    public function index()
    {
        $demandes = Demande::where('employe_id', auth()->id())
                           ->with('article')
                           ->orderBy('created_at','desc')
                           ->paginate(10);

        return view('demandes.index', compact('demandes'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $articles = Article::all();
        return view('demandes.create', compact('articles'));
    }

    /**
     * Enregistrer une demande
     */
public function store(Request $request)
{
    $request->validate([
        'article_id' => 'required|exists:articles,id',
        'quantite_cartons' => 'nullable|integer|min:0',
        'quantite_pieces'  => 'nullable|integer|min:0',
        'remarque' => 'nullable|string',
        'gestionnaire_id' => 'required|exists:users,id',
    ]);

    $article = Article::findOrFail($request->article_id);

    $quantiteCartons = $request->quantite_cartons ?? 0;
    $quantitePieces  = $request->quantite_pieces ?? 0;

    $quantiteTotal = ($quantiteCartons * $article->contenance_carton) + $quantitePieces;

    $demande = Demande::create([
        'employe_id'       => auth()->id(),
        'article_id'       => $article->id,
        'quantite_cartons' => $quantiteCartons,
        'quantite_pieces'  => $quantitePieces,
        'quantite_total'   => $quantiteTotal,
        'remarque'         => $request->remarque,
        'date'             => now()->toDateString(),
        'statut'           => 'en_attente',
    ]);

    // ✅ récupérer le gestionnaire choisi
    $gestionnaire = \App\Models\User::findOrFail($request->gestionnaire_id);

    // ✅ vérifier qu’il a bien le rôle Gestionnaire
    if (!$gestionnaire->hasRole('Gestionnaire')) {
        return back()->withErrors(['gestionnaire_id' => 'L’utilisateur choisi n’est pas un gestionnaire.']);
    }

    // ✅ notifier uniquement lui
    $gestionnaire->notify(new \App\Notifications\NouvelleDemandeNotification($demande));

    return redirect()->route('demandes.index')->with('success','Demande créée et envoyée au gestionnaire choisi.');
}



    /**
     * Validation par le gestionnaire
     */
    public function all()
{
    $demandes = Demande::with(['article','employe'])
                        ->orderBy('created_at','desc')
                        ->paginate(10);

    return view('demandes.all', compact('demandes'));
}

public function valider(Demande $demande)
{
    $article = $demande->article;

    
    $article->stock = max(0, $article->stock - $demande->quantite_total);
    $article->save();

    $demande->update(['statut' => 'validee']);

    return back()->with('success','Demande validée et stock mis à jour.');
}

public function refuser(Demande $demande, Request $request)
{
    $demande->update([
        'statut' => 'refusee',
        'remarque' => $request->remarque,
    ]);

    return back()->with('success','Demande refusée.');
}
public function show($id)
{
    $demande = Demande::with(['article','employe'])->findOrFail($id);
    return view('demandes.show', compact('demande'));
}

}
