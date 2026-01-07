<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sortie extends Model
{
    protected $fillable = ['destination','motif','commentaire','date_sortie']; 

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_sortie')
                    ->withPivot('quantite_total','quantite_cartons','quantite_pieces')
                    ->withTimestamps();
    }
}
