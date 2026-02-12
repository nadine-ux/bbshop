<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'nom',
        'categorie_id',
        'code_barres',
        'photo',
        'date_peremption',
        'quantite_minimale',
        'prix_achat',
        'fournisseur_id',
        'description',
        'contenance_carton',
        'stock',
        'marque_id'
    ];

    // 🔑 Relation avec fournisseur
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    // 🔑 Relation avec catégorie (hiérarchie Category)
    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    // 🔑 Relation avec les entrées (Many-to-Many via pivot)
    public function entrees()
    {
        return $this->belongsToMany(Entree::class, 'article_entree')
                    ->withPivot('quantite_cartons','quantite_pieces','quantite_total','prix_unitaire')
                    ->withTimestamps();
    }

    // 🔑 Relation avec les sorties (Many-to-Many via pivot)
    public function sorties()
    {
        return $this->belongsToMany(Sortie::class, 'article_sortie')
                    ->withPivot('quantite_cartons','quantite_pieces','quantite_total')
                    ->withTimestamps();
    }

    // 🔑 Relation avec les demandes (si tu as un module demandes)
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
    public function getStockCritiqueAttribute()
{
    return $this->stock <= $this->quantite_minimale;
}
// Dans la classe Article, ajoutez :

public function inventaires()
{
    return $this->hasMany(Inventaire::class)->orderBy('date_mouvement', 'desc');
}
    public function marque()
    {
        return $this->belongsTo(Marque::class);
    }
}
