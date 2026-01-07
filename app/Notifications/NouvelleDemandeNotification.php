<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification
{  
    use Queueable;

    protected $demande;

    /**
     * Create a new notification instance.
     */
    public function __construct(Demande $demande)
    {
        $this->demande = $demande;   
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    /**
     * Stocker la notification en base
     */
    public function toDatabase(object $notifiable): array
    {
        return [ 
            'demande_id' => $this->demande->id, 
            'article'    => $this->demande->article->nom, 
            'employe'    => $this->demande->employe->name, 
            'quantite'   => $this->demande->quantite_total, 
        ];
    }

    /**
     * Représentation alternative (si tu veux utiliser toArray)
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
