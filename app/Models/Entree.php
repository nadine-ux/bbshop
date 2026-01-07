<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entree extends Model
{
    protected $fillable = ['fournisseur_id','date_reception','commentaire','user_id'];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function lignes()
    {
        return $this->hasMany(EntreeLigne::class);
    }
   public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_entree')
                    ->withPivot('quantite_cartons','quantite_pieces','quantite_total','prix_unitaire')
                    ->withTimestamps();
    }
    public function gestionnaire()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
