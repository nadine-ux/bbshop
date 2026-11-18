<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeStatutNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Demande $demande,
        protected string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'demande_id' => $this->demande->id,
            'statut'     => $this->demande->statut,
            'message'    => $this->message,
            'type'       => 'demande_statut',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}