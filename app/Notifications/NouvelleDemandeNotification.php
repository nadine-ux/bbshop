<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification
{
    use Queueable;

    protected Demande $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->demande->loadMissing('employe', 'lignes');

        return [
            'demande_id'  => $this->demande->id,
            'employe'     => $this->demande->employe->name,
            'nb_articles' => $this->demande->lignes->count(),
            'message'     => "Nouvelle demande #{$this->demande->id} de {$this->demande->employe->name} ({$this->demande->lignes->count()} article(s))",
            'type'        => 'demande',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}