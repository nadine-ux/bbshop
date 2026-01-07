<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NouvelleCommandeNotification;

class CommandeController extends Controller
{
    public function index()
    {
        // Seuls les directeurs peuvent voir toutes les commandes
        $this->authorize('viewAny', Commande::class);

        $commandes = Commande::with(['article','user'])->latest()->paginate(10);
        return view('commandes.index', compact('commandes'));
    }

    public function create()
    {
        // Seuls les gestionnaires peuvent créer
        $this->authorize('create', Commande::class);

        $articles = Article::all();
        return view('commandes.create', compact('articles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Commande::class);

        $request->validate([
            'article_id'       => 'required|exists:articles,id',
            'quantite_cartons' => 'nullable|integer|min:0',
            'quantite_pieces'  => 'nullable|integer|min:0',
            'remarque'         => 'nullable|string',
            'date'             => 'nullable|date',
        ]);

        $article = Article::findOrFail($request->article_id);

        $cartons = $request->quantite_cartons ?? 0;
        $pieces  = $request->quantite_pieces ?? 0;
        $quantiteTotal = ($cartons * $article->contenance_carton) + $pieces;

        $commande = Commande::create([
            'user_id'          => auth()->id(),
            'article_id'       => $article->id,
            'quantite_cartons' => $cartons,
            'quantite_pieces'  => $pieces,
            'quantite_total'   => $quantiteTotal,
            'statut'           => 'en_attente',
            'remarque'         => $request->remarque,
            'date'             => $request->date ?? now(),
        ]);

        // Notification aux directeurs
        $directeurs = \App\Models\User::role('Directeur')->get();
        Notification::send($directeurs, new NouvelleCommandeNotification($commande));

        return redirect()->route('commandes.index')->with('success', 'Commande créée et notification envoyée au directeur.');
    }

    public function show(Commande $commande)
    {
        $this->authorize('view', $commande);
        return view('commandes.show', compact('commande'));
    }

    public function edit(Commande $commande)
    {
        $this->authorize('update', $commande);
        $articles = Article::all();
        return view('commandes.edit', compact('commande','articles'));
    }

    public function update(Request $request, Commande $commande)
    {
        $this->authorize('update', $commande);

        $request->validate([
            'article_id'       => 'required|exists:articles,id',
            'quantite_cartons' => 'nullable|integer|min:0',
            'quantite_pieces'  => 'nullable|integer|min:0',
            'remarque'         => 'nullable|string',
            'date'             => 'nullable|date',
        ]);

        $article = Article::findOrFail($request->article_id);

        $cartons = $request->quantite_cartons ?? 0;
        $pieces  = $request->quantite_pieces ?? 0;
        $quantiteTotal = ($cartons * $article->contenance_carton) + $pieces;

        $commande->update([
            'article_id'       => $article->id,
            'quantite_cartons' => $cartons,
            'quantite_pieces'  => $pieces,
            'quantite_total'   => $quantiteTotal,
            'remarque'         => $request->remarque,
            'date'             => $request->date,
        ]);

        return redirect()->route('commandes.index')->with('success', 'Commande mise à jour.');
    }
}
