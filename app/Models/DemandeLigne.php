<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeLigne extends Model
{
    protected $fillable = [
        'demande_id', 'article_id',
        'quantite_cartons', 'quantite_pieces', 'quantite_total',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}