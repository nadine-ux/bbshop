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
   public function index()
{
    $entrees = Entree::with(['fournisseur', 'gestionnaire', 'articles']) 
        ->orderBy('date_reception', 'desc')
        ->paginate(15);

    return view('entrees.index', compact('entrees'));
}

    public function create()
    {
        $fournisseurs = Fournisseur::all();
        $articles = Article::with('category')->get();

        return view('entrees.create', compact('fournisseurs', 'articles'));
    }

public function store(Request $request)
{
    $request->validate([
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'date_reception' => 'required|date',
        'commentaire' => 'nullable|string',
        'articles' => 'required|array|min:1',
        'articles.*.article_id' => 'required|exists:articles,id',
        'articles.*.quantite_cartons' => 'required|integer|min:0',
        'articles.*.quantite_pieces' => 'required|integer|min:0',
        'articles.*.prix_unitaire' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();
    try {
        // 1️⃣ Créer l'entrée
        $entree = Entree::create([
            'fournisseur_id' => $request->fournisseur_id,
            'date_reception' => $request->date_reception,
            'commentaire' => $request->commentaire,
            'user_id' => auth()->id(),
        ]);

        \Log::info("Entrée créée : ID = {$entree->id}");
        \Log::info("Nombre d'articles à traiter : " . count($request->articles));

        // 2️⃣ Boucle sur les articles
        foreach ($request->articles as $index => $articleData) {
            \Log::info("Traitement article index $index", $articleData);
            
            try {
                $article = Article::findOrFail($articleData['article_id']);
                \Log::info("Article trouvé : {$article->id} - {$article->nom}");
                
                // Calculer quantité totale
                $quantiteCartons = (int) $articleData['quantite_cartons'];
                $quantitePieces = (int) $articleData['quantite_pieces'];
                $quantiteTotal = ($quantiteCartons * $article->contenance_carton) + $quantitePieces;

                \Log::info("Quantités calculées : cartons=$quantiteCartons, pieces=$quantitePieces, total=$quantiteTotal");

                // Attacher l'article à l'entrée (pivot)
                $entree->articles()->attach($article->id, [
                    'quantite_cartons' => $quantiteCartons,
                    'quantite_pieces' => $quantitePieces,
                    'quantite_total' => $quantiteTotal,
                    'prix_unitaire' => $articleData['prix_unitaire'],
                ]);

                \Log::info("Article {$article->id} attaché avec succès");

                // 3️⃣ Enregistrer dans l'inventaire
                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'entree',
                    quantite: $quantiteTotal,
                    prixUnitaire: $articleData['prix_unitaire'],
                    entreeId: $entree->id,
                    motif: "Réception fournisseur",
                    commentaire: $request->commentaire
                );

                \Log::info("Mouvement inventaire enregistré pour article {$article->id}");

            } catch (\Exception $e) {
                \Log::error("ERREUR sur article index $index : " . $e->getMessage());
                \Log::error($e->getTraceAsString());
                throw $e; // Re-lancer l'erreur pour annuler la transaction
            }
        }

        \Log::info("Tous les articles traités avec succès");

        DB::commit();

        return redirect()->route('entrees.index')
            ->with('success', 'Entrée enregistrée avec succès');

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('ERREUR GLOBALE store entree : ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
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
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'date_reception' => 'required|date',
            'commentaire' => 'nullable|string',
        ]);

        $entree->update($request->only(['fournisseur_id', 'date_reception', 'commentaire']));

        return redirect()->route('entrees.show', $entree)
            ->with('success', 'Entrée mise à jour avec succès');
    }

    public function destroy(Entree $entree)
    {
        DB::beginTransaction();
        try {
            // 1️⃣ Récupérer les articles avant suppression
            $articlesData = $entree->articles()->get();

            // 2️⃣ Annuler les mouvements de stock
            foreach ($articlesData as $article) {
                $quantiteTotal = $article->pivot->quantite_total;

                // Créer un mouvement d'ajustement inverse
                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'sortie',
                    quantite: $quantiteTotal,
                    motif: "Annulation entrée #{$entree->id}",
                    commentaire: "Suppression de l'entrée"
                );
            }

            // 3️⃣ Supprimer l'entrée (cascade supprimera les pivots)
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