<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleBarcode extends Model
{
    protected $fillable = [
        'article_id',
        'code_barres',
        'label',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * S'assure qu'un seul code-barres est marqué "primary" par article.
     * À appeler après avoir défini is_primary = true sur un barcode.
     */
    public static function enforceSinglePrimary(int $articleId, int $exceptId): void
    {
        static::where('article_id', $articleId)
              ->where('id', '!=', $exceptId)
              ->update(['is_primary' => false]);
    }
}