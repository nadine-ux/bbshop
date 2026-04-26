<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NouvelleCommandeNotification;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Commande::class);

        $query = Commande::with(['article', 'user'])->latest();

        // Recherche texte
        if ($q = $request->q) {
            $query->where(function ($q2) use ($q) {
                $q2->whereHas('user', fn($u) => $u->where('name', 'like', "%$q%"))
                   ->orWhere('id', 'like', "%$q%");
            });
        }

        // Filtre statut
        if ($statut = $request->statut) {
            $query->where('statut', $statut);
        }

        // Filtre gestionnaire
        if ($gId = $request->gestionnaire_id) {
            $query->where('user_id', $gId);
        }

        // Tri
        match ($request->sort) {
            'numero'       => $query->orderBy('id', 'desc'),
            'statut'       => $query->orderBy('statut'),
            'gestionnaire' => $query->orderByJoin('user.name'),
            default        => $query->latest(),
        };

        $commandes    = $query->paginate(10)->withQueryString();
        $gestionnaires = \App\Models\User::role('Gestionnaire')->orderBy('name')->get();

        return view('commandes.index', compact('commandes', 'gestionnaires'));
    }

    public function create()
    {
        $this->authorize('create', Commande::class);

        $articlesData = Article::select('id', 'nom', 'code_barres', 'stock', 'quantite_minimale')
            ->orderBy('nom')
            ->get()
            ->map(fn($a) => [
                'id'              => $a->id,
                'nom'             => $a->nom,
                'code_barres'     => $a->code_barres,
                'stock'           => $a->stock,
                'quantite_alerte' => $a->quantite_minimale,
            ])
            ->toArray();

        return view('commandes.create', compact('articlesData'));
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

        $cartons       = $request->quantite_cartons ?? 0;
        $pieces        = $request->quantite_pieces  ?? 0;
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

        $directeurs = \App\Models\User::role('Directeur')->get();
        Notification::send($directeurs, new NouvelleCommandeNotification($commande));

        return redirect()->route('commandes.index')
            ->with('success', 'Commande créée et notification envoyée au directeur.');
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
        return view('commandes.edit', compact('commande', 'articles'));
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

        $cartons       = $request->quantite_cartons ?? 0;
        $pieces        = $request->quantite_pieces  ?? 0;
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

    // ─── DETAIL JSON (popup) ───────────────────────────────────────────────────
    public function detailJson(Commande $commande)
    {
        $this->authorize('view', $commande);

        $commande->load('user', 'article');

        return response()->json([
            'id'          => $commande->id,
            'statut'      => $commande->statut,
            'date'        => $commande->date
                                ? \Carbon\Carbon::parse($commande->date)->format('d.m.Y')
                                : \Carbon\Carbon::parse($commande->created_at)->format('d.m.Y'),
            'gestionnaire' => $commande->user->name ?? '—',
            'remarque'    => $commande->remarque,

            // La blade attend un tableau "lignes" avec ces clés
            'lignes' => [[
                'article_nom'       => $commande->article->nom          ?? '—',
                'quantite_restante' => $commande->article->stock        ?? 0,
                'quantite_alerte'   => $commande->article->quantite_minimale ?? 0,
                'quantite_demandee' => $commande->quantite_total        ?? 0,
            ]],
        ]);
    }

    // ─── VALIDER ───────────────────────────────────────────────────────────────
    public function valider(Request $request, Commande $commande)
    {
        $this->authorize('update', $commande);

        if ($commande->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut plus être modifiée.',
            ], 422);
        }

        // Récupérer la quantité éventuellement modifiée par le directeur
        // La blade envoie : quantites = [{index:0, quantite: X}]
        $quantites = $request->input('quantites', []);
        $nouvelleQte = collect($quantites)->firstWhere('index', 0)['quantite'] ?? null;

        if (!is_null($nouvelleQte) && $nouvelleQte >= 0) {
            $commande->quantite_total = (int) $nouvelleQte;
        }

        $commande->statut = 'validee';
        $commande->save();

        return response()->json(['success' => true]);
    }

    // ─── ANNULER ───────────────────────────────────────────────────────────────
    public function annuler(Commande $commande)
    {
        $this->authorize('update', $commande);

        if ($commande->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut plus être annulée.',
            ], 422);
        }

        $commande->update(['statut' => 'annulee']);

        return response()->json(['success' => true]);
    }
}