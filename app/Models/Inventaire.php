<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    protected $fillable = [
        'article_id',
        'type',
        'quantite',
        'stock_avant',
        'stock_apres',
        'prix_unitaire',
        'entree_id',
        'sortie_id',
        'motif',
        'commentaire',
        'user_id',
        'date_mouvement',
        'date'
    ];

    protected $casts = [
        'date_mouvement' => 'datetime',
        'prix_unitaire' => 'decimal:2'
    ];

    // 🔑 Relations
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function entree()
    {
        return $this->belongsTo(Entree::class);
    }

    public function sortie()
    {
        return $this->belongsTo(Sortie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 📊 Scopes utiles
    public function scopeEntrees($query)
    {
        return $query->where('type', 'entree');
    }

    public function scopeSorties($query)
    {
        return $query->where('type', 'sortie');
    }

    public function scopePourArticle($query, $articleId)
    {
        return $query->where('article_id', $articleId);
    }

    public function scopePeriode($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date_mouvement', [$dateDebut, $dateFin]);
    }

    // 🎨 Accesseurs
    public function getTypeLibelleAttribute()
    {
        return match($this->type) {
            'entree' => '📥 Entrée',
            'sortie' => '📤 Sortie',
            'ajustement' => '🔧 Ajustement',
            'inventaire_initial' => '📋 Stock initial',
            default => $this->type
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'entree' => 'success',
            'sortie' => 'danger',
            'ajustement' => 'warning',
            'inventaire_initial' => 'info',
            default => 'secondary'
        };
    }
}