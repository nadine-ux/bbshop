<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    protected $fillable = ['type','date','ecarts'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'inventaire_articles')
                    ->withPivot('quantite_reelle','quantite_theorique');
    }
}
