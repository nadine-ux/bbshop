<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Demande;
use App\Models\User;
use App\Notifications\DemandeStatutNotification;
use App\Notifications\NouvelleDemandeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemandeController extends Controller
{
    /* ------------------------------------------------------------------
     |  EMPLOYÉ
     * ------------------------------------------------------------------ */

    /** Mes demandes */
   public function index(Request $request)
{
    $demandes = Demande::where('employe_id', auth()->id())
        ->with(['gestionnaire', 'lignes.article'])
        ->when($request->statut, fn ($q, $s) => $q->where('statut', $s))
        ->when($request->article, function ($q, $nom) {
            $q->whereHas('lignes.article', fn ($a) => $a->where('nom', 'like', "%{$nom}%"));
        })
        ->latest()
        ->paginate(10)
        ->appends($request->query());

    return view('demandes.index', compact('demandes'));
}

    /** Formulaire : scan automatique des articles en rupture */
   public function create()
{
    $articles = Article::with('barcodes')
        ->orderBy('nom')
        ->get()
        ->map(function ($article) {
            $stock      = (int) $article->stock;
            $min        = (int) $article->quantite_minimale;
            $manque     = max(0, (int) $article->quantite_voulue - $stock);
            $contenance = max(1, (int) $article->contenance_carton);

            $article->etat             = $stock <= 0 ? 'rupture' : ($stock <= $min ? 'faible' : 'normal');
            $article->manque           = $manque;
            $article->cartons_suggeres = intdiv($manque, $contenance);
            $article->pieces_suggerees = $manque % $contenance;

            // Tous les codes-barres de l'article (principal + secondaires)
            $article->codes = collect([$article->code_barres])
                ->merge($article->barcodes->pluck('code_barres'))
                ->filter()->map(fn ($c) => trim($c))->unique()->values();

            return $article;
        });

    $gestionnaires = User::role('Gestionnaire')->orderBy('name')->get();

    return view('demandes.create', compact('articles', 'gestionnaires'));
}

    /** Enregistrer la demande (plusieurs articles) */
    public function store(Request $request)
    {
        $request->validate([
            'gestionnaire_id'      => 'required|exists:users,id',
            'remarque'             => 'nullable|string|max:1000',
            'lignes'               => 'required|array|min:1',
            'lignes.*.selected'    => 'nullable',
            'lignes.*.cartons'     => 'nullable|integer|min:0',
            'lignes.*.pieces'      => 'nullable|integer|min:0',
        ]);

        // Le gestionnaire choisi doit avoir le bon rôle (vérifié AVANT de créer quoi que ce soit)
        $gestionnaire = User::findOrFail($request->gestionnaire_id);
        if (!$gestionnaire->hasRole('Gestionnaire')) {
            return back()->withInput()
                ->withErrors(['gestionnaire_id' => "L'utilisateur choisi n'est pas un gestionnaire."]);
        }

        // Construire les lignes à partir des articles cochés
        $articles = Article::whereIn('id', array_keys($request->lignes))->get()->keyBy('id');
        $lignes   = [];

        foreach ($request->lignes as $articleId => $data) {
            if (empty($data['selected']) || !isset($articles[$articleId])) {
                continue;
            }

            $article    = $articles[$articleId];
            $cartons    = (int) ($data['cartons'] ?? 0);
            $pieces     = (int) ($data['pieces'] ?? 0);
            $contenance = max(1, (int) $article->contenance_carton);
            $total      = ($cartons * $contenance) + $pieces;

            if ($total <= 0) {
                continue;
            }

            $lignes[] = [
                'article_id'       => $article->id,
                'quantite_cartons' => $cartons,
                'quantite_pieces'  => $pieces,
                'quantite_total'   => $total,
            ];
        }

        if (empty($lignes)) {
            return back()->withInput()
                ->withErrors(['lignes' => 'Sélectionne au moins un article avec une quantité supérieure à 0.']);
        }

        $demande = DB::transaction(function () use ($request, $lignes) {
            $demande = Demande::create([
                'employe_id'      => auth()->id(),
                'gestionnaire_id' => $request->gestionnaire_id,
                'remarque'        => $request->remarque,
                'date'            => now()->toDateString(),
                'statut'          => 'en_attente',
            ]);

            $demande->lignes()->createMany($lignes);

            return $demande;
        });

        $gestionnaire->notify(new NouvelleDemandeNotification($demande));

        return redirect()->route('demandes.index')
            ->with('success', 'Demande créée et envoyée au gestionnaire choisi.');
    }

    /** L'employé confirme la réception de la commande */
  /** L'employé confirme la réception de la commande : le stock augmente ici */
public function recevoir(Demande $demande)
{
    // Seul l'employé qui a créé la demande peut la confirmer
    if ($demande->employe_id !== auth()->id()) {
        abort(403);
    }

    if ($demande->statut !== 'validee') {
        return back()->with('error', 'Cette demande ne peut pas être marquée comme reçue.');
    }

    DB::transaction(function () use ($demande) {
        $demande->load('lignes');

        // Ajouter les quantités reçues au stock de chaque article
        foreach ($demande->lignes as $ligne) {
            Article::where('id', $ligne->article_id)
                ->increment('stock', $ligne->quantite_total);
        }

        $demande->update([
            'statut'         => 'recue',
            'date_reception' => now(),
        ]);
    });

    // Prévenir le gestionnaire (hors transaction)
    $demande->loadMissing('gestionnaire', 'employe');

    if ($demande->gestionnaire) {
        $demande->gestionnaire->notify(new DemandeStatutNotification(
            $demande,
            "Commande #{$demande->id} reçue par {$demande->employe->name}."
        ));
    }

    return back()->with('success', 'Réception confirmée : le stock a été mis à jour.');
}

    /* ------------------------------------------------------------------
     |  GESTIONNAIRE
     * ------------------------------------------------------------------ */

    /** Demandes reçues par le gestionnaire connecté (le Directeur voit tout) */
    public function all()
    {
        $query = Demande::with(['employe', 'lignes.article'])->latest();

        if (!auth()->user()->hasRole('Directeur')) {
            $query->where('gestionnaire_id', auth()->id());
        }

        $demandes = $query->paginate(10);

        return view('demandes.all', compact('demandes'));
    }

    /** Préparer + valider : le stock est décrémenté ici */
   public function valider(Demande $demande)
{
    $this->autoriserGestion($demande);

    if ($demande->statut !== 'en_attente') {
        return back()->with('error', 'Cette demande a déjà été traitée.');
    }

    $demande->update([
        'statut'          => 'validee',
        'date_validation' => now(),
    ]);

    $demande->employe->notify(new DemandeStatutNotification(
        $demande,
        "Ta demande #{$demande->id} est prête et envoyée : confirme la réception à l'arrivée."
    ));

    return back()->with('success', 'Demande validée et envoyée.');
}

    public function refuser(Demande $demande, Request $request)
    {
        $this->autoriserGestion($demande);

        $request->validate(['motif_refus' => 'required|string|max:1000']);

        if ($demande->statut !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $demande->update([
            'statut'      => 'refusee',
            'motif_refus' => $request->motif_refus,
        ]);

        $demande->employe->notify(new DemandeStatutNotification(
            $demande,
            "Ta demande #{$demande->id} a été refusée : {$request->motif_refus}"
        ));

        return back()->with('success', 'Demande refusée.');
    }

    /* ------------------------------------------------------------------
     |  COMMUN
     * ------------------------------------------------------------------ */

    public function show(Demande $demande)
    {
        $user = auth()->user();

        $autorise = $demande->employe_id === $user->id
            || $demande->gestionnaire_id === $user->id
            || $user->hasRole('Directeur');

        abort_unless($autorise, 403);

        $demande->load(['employe', 'gestionnaire', 'lignes.article']);

        return view('demandes.show', compact('demande'));
    }

    /** Clic sur une notification : la marquer lue puis ouvrir la demande */
    public function lireNotification(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $demandeId = $notification->data['demande_id'] ?? null;

        return $demandeId
            ? redirect()->route('demandes.show', $demandeId)
            : redirect()->route('demandes.index');
    }

    private function autoriserGestion(Demande $demande): void
    {
        $user = auth()->user();

        abort_unless(
            $user->hasRole('Directeur') || $demande->gestionnaire_id === $user->id,
            403,
            "Cette demande n'est pas adressée à toi."
        );
    }
}