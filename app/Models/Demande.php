<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'employe_id','article_id',
        'quantite_cartons','quantite_pieces','quantite_total',
        'statut','remarque','date'
    ];

    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
