<?php

namespace App\Http\Controllers;

use App\Models\Sortie;
use App\Models\Article;
use App\Services\InventaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SortieController extends Controller
{
    public function index()
    {
        $sorties = Sortie::with('articles')
            ->orderBy('motif', 'desc')
            ->paginate(15);

        return view('sorties.index', compact('sorties'));
    }

    public function create()
    {
        $articles = Article::with(['category', 'barcodes'])->where('stock', '>', 0)->get();

        $articlesData = [];
        foreach ($articles as $a) {
            $primaryBarcode = $a->barcodes->firstWhere('is_primary', true)
                            ?? $a->barcodes->first();

            $articlesData[] = [
                'id'                => $a->id,
                'nom'               => $a->nom,
                'contenance_carton' => $a->contenance_carton,
                'stock'             => $a->stock,
                'code_barres'       => $primaryBarcode?->code_barres ?? $a->code_barres ?? '',
                'reference'         => $a->reference ?? '',
            ];
        }

        return view('sorties.create', compact('articles', 'articlesData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination' => 'required|string|max:255',
            'motif' => 'required|string|max:255',
         
            'commentaire' => 'nullable|string',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantite_cartons' => 'required|integer|min:0',
            'articles.*.quantite_pieces' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1️⃣ Créer la sortie
            $sortie = Sortie::create([
                'destination' => $request->destination,
                'motif' => $request->motif,
                
                'commentaire' => $request->commentaire,
            ]);

            // 2️⃣ Traiter les articles
            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['article_id']);

                // Calculer quantité totale
                $quantiteCartons = (int) $articleData['quantite_cartons'];
                $quantitePieces = (int) $articleData['quantite_pieces'];
                $quantiteTotal = ($quantiteCartons * $article->contenance_carton) + $quantitePieces;

                // Vérifier stock disponible
                if ($article->stock < $quantiteTotal) {
                    throw new \Exception("Stock insuffisant pour {$article->nom}. Disponible: {$article->stock}, Demandé: {$quantiteTotal}");
                }

                // Attacher à la sortie
                $sortie->articles()->attach($article->id, [
                    'quantite_cartons' => $quantiteCartons,
                    'quantite_pieces' => $quantitePieces,
                    'quantite_total' => $quantiteTotal,
                ]);

                // 3️⃣ 🆕 Enregistrer dans l'inventaire
                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'sortie',
                    quantite: $quantiteTotal,
                    sortieId: $sortie->id,
                    motif: $request->motif,
                    commentaire: "Destination: {$request->destination}"
                );
            }

            DB::commit();

            return redirect()->route('sorties.index')
                ->with('success', 'Sortie enregistrée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Sortie $sortie)
    {
        $sortie->load('articles');
        return view('sorties.show', compact('sortie'));
    }

    public function destroy(Sortie $sortie)
    {
        DB::beginTransaction();
        try {
            // Annuler les mouvements
            foreach ($sortie->articles as $article) {
                $quantiteTotal = $article->pivot->quantite_total;

                InventaireService::enregistrerMouvement(
                    article: $article,
                    type: 'entree',
                    quantite: $quantiteTotal,
                    motif: "Annulation sortie #{$sortie->id}",
                    commentaire: "Suppression de la sortie"
                );
            }

            $sortie->delete();
            DB::commit();

            return redirect()->route('sorties.index')
                ->with('success', 'Sortie supprimée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}