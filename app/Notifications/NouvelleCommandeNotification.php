<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Commande;

class NouvelleCommandeNotification extends Notification
{
    use Queueable;

    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function via($notifiable)
    {
        // Notification en base + email si configuré
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle commande créée')
            ->greeting('Bonjour Admin')
            ->line('Une nouvelle commande a été créée.')
            ->line('Article : ' . $this->commande->article->nom)
            ->line('Quantité totale : ' . $this->commande->quantite_total)
            ->line('Créée par : ' . $this->commande->user->name)
            ->action('Voir la commande', route('commandes.show', $this->commande->id))
            ->line('Merci de vérifier cette commande.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'commande_id' => $this->commande->id,
            'article'     => $this->commande->article->nom,
            'quantite'    => $this->commande->quantite_total,
            'gestionnaire'        => $this->commande->user->name,
            'date'        => $this->commande->date,
        ];
    }
}
