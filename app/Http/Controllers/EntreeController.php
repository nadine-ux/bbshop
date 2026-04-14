<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use App\Models\Article;
use App\Models\Fournisseur;
use App\Services\InventaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntreeController extends Controller
{
public function index(Request $request)
{
    $query = Entree::with(['fournisseur', 'gestionnaire', 'articles']);

    if ($request->filled('date')) {
        $query->whereDate('date_reception', $request->date);
    }
    if ($request->filled('mois')) {
        $query->whereMonth('date_reception', $request->mois);
    }
    if ($request->filled('annee')) {
        $query->whereYear('date_reception', $request->annee);
    }
    if ($request->filled('fournisseur')) {
        $query->whereHas('fournisseur', function($q) use ($request) {
            $q->where('nom', 'like', '%' . $request->fournisseur . '%');
        });
    }

    $sortField = $request->get('sort', 'date_reception');
    $sortDir   = $request->get('direction', 'desc');

    if ($sortField === 'fournisseur') {
        $query->join('fournisseurs', 'entrees.fournisseur_id', '=', 'fournisseurs.id')
              ->orderBy('fournisseurs.nom', $sortDir === 'asc' ? 'asc' : 'desc')
              ->select('entrees.*');
    } elseif (in_array($sortField, ['date_reception', 'id'])) {
        $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
    }

    $entrees = $query->paginate(15)->withQueryString();

    $annees = Entree::selectRaw('YEAR(date_reception) as annee')
        ->distinct()->orderBy('annee', 'desc')->pluck('annee');

    return view('entrees.index', compact('entrees', 'annees'));
}
   public function create()
{
    $fournisseurs = Fournisseur::all();
    $articles = Article::with(['category', 'barcodes'])->get(); // ← charger barcodes

    $articlesData = [];
    foreach ($articles as $a) {
        // Récupérer le code-barres primaire
        $primaryBarcode = $a->barcodes->firstWhere('is_primary', true)
                        ?? $a->barcodes->first();

        $articlesData[] = [
            'id'                => $a->id,
            'nom'               => $a->nom,
            'contenance_carton' => $a->contenance_carton,
            'prix_achat'        => $a->prix_achat,
            'code_barres'       => $primaryBarcode?->code_barres ?? $a->code_barres ?? '',
            'reference'         => $a->reference ?? '',
        ];
    }

    return view('entrees.create', compact('fournisseurs', 'articles', 'articlesData'));
}

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'               => 'required|exists:fournisseurs,id',
            'date_reception'               => 'required|date',
            'commentaire'                  => 'nullable|string',
            'remise_globale'               => 'nullable|numeric|min:0|max:100',
            'articles'                     => 'required|array|min:1',
            'articles.*.article_id'        => 'required|exists:articles,id',
            'articles.*.quantite_cartons'  => 'required|integer|min:0',
            'articles.*.quantite_pieces'   => 'required|integer|min:0',
            'articles.*.prix_unitaire'     => 'required|numeric|min:0',
            'articles.*.remise'            => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $entree = Entree::create([
                'fournisseur_id'  => $request->fournisseur_id,
                'date_reception'  => $request->date_reception,
                'commentaire'     => $request->commentaire,
                'remise_globale'  => $request->remise_globale ?? 0,
                'user_id'         => auth()->id(),
            ]);

            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['article_id']);

                $quantiteCartons = (int) $articleData['quantite_cartons'];
                $quantitePieces  = (int) $articleData['quantite_pieces'];
                $quantiteTotal   = ($quantiteCartons * $article->contenance_carton) + $quantitePieces;
                $remise          = isset($articleData['remise']) ? (float) $articleData['remise'] : 0;
                $prixApresRemise = $articleData['prix_unitaire'] * (1 - $remise / 100);

                $entree->articles()->attach($article->id, [
                    'quantite_cartons' => $quantiteCartons,
                    'quantite_pieces'  => $quantitePieces,
                    'quantite_total'   => $quantiteTotal,
                    'prix_unitaire'    => $articleData['prix_unitaire'],
                    'remise'           => $remise,
                ]);

                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'entree',
                    quantite: $quantiteTotal,
                    prixUnitaire: $prixApresRemise,
                    entreeId: $entree->id,
                    motif: "Réception fournisseur",
                    commentaire: $request->commentaire
                );
            }

            DB::commit();

            return redirect()->route('entrees.index')
                ->with('success', 'Entrée enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function show(Entree $entree)
    {
        $entree->load(['fournisseur', 'articles', 'gestionnaire']);
        return view('entrees.show', compact('entree'));
    }

    public function edit(Entree $entree)
    {
        $entree->load('articles');
        $fournisseurs = Fournisseur::all();
        $articles = Article::with('category')->get();

        return view('entrees.edit', compact('entree', 'fournisseurs', 'articles'));
    }

    public function update(Request $request, Entree $entree)
    {
        $request->validate([
            'fournisseur_id'  => 'required|exists:fournisseurs,id',
            'date_reception'  => 'required|date',
            'commentaire'     => 'nullable|string',
            'remise_globale'  => 'nullable|numeric|min:0|max:100',
        ]);

        $entree->update($request->only(['fournisseur_id', 'date_reception', 'commentaire', 'remise_globale']));

        return redirect()->route('entrees.show', $entree)
            ->with('success', 'Entrée mise à jour avec succès');
    }

    public function destroy(Entree $entree)
    {
        DB::beginTransaction();
        try {
            $articlesData = $entree->articles()->get();

            foreach ($articlesData as $article) {
                $quantiteTotal = $article->pivot->quantite_total;

                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'sortie',
                    quantite: $quantiteTotal,
                    motif: "Annulation entrée #{$entree->id}",
                    commentaire: "Suppression de l'entrée"
                );
            }

            $entree->delete();

            DB::commit();

            return redirect()->route('entrees.index')
                ->with('success', 'Entrée supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
}