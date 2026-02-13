<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Inventaire;
use Illuminate\Support\Facades\Auth;

class InventaireService
{
    /**
     * Enregistre un mouvement d'inventaire
     */
    public static function enregistrerMouvement(
        Article $article,
        string $type,
        int $quantite,
        ?float $prixUnitaire = null,
        ?int $entreeId = null,
        ?int $sortieId = null,
        ?string $motif = null,
        ?string $commentaire = null
    ) {
        $stockAvant = $article->stock;
        
        // Calcul du nouveau stock
        $stockApres = match($type) {
            'entree' => $stockAvant + $quantite,
            'sortie' => $stockAvant - $quantite,
            'ajustement' => $quantite, // Quantité = nouveau stock
            'inventaire_initial' => $quantite,
            default => $stockAvant
        };

        // Créer l'enregistrement d'inventaire
        $inventaire = Inventaire::create([
            'article_id' => $article->id,
            'type' => $type,
            'quantite' => $type === 'ajustement' ? ($stockApres - $stockAvant) : $quantite,
            'stock_avant' => $stockAvant,
            'stock_apres' => $stockApres,
            'prix_unitaire' => $prixUnitaire,
            'entree_id' => $entreeId,
            'sortie_id' => $sortieId,
            'motif' => $motif,
            'commentaire' => $commentaire,
            'user_id' => Auth::id(),
            'date' => now(), 
            'date_mouvement' => now()
        ]);

        // Mettre à jour le stock de l'article
        $article->update(['stock' => $stockApres]);

        return $inventaire;
    }
}