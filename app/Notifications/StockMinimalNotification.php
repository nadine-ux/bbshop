<?php
namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockMinimalNotification extends Notification
{
    use Queueable;

    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'article_id' => $this->article->id,
            'article'    => $this->article->nom,
            'stock'      => $this->article->stock,
            'quantite_minimale' => $this->article->quantite_minimale,
            'message'    => "⚠️ Stock minimal atteint pour {$this->article->nom} : {$this->article->stock} unités restantes."
        ];
    }
}

