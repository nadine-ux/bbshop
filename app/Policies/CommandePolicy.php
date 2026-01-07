<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Commande;

class CommandePolicy
{
    // Liste des commandes
    public function viewAny(User $user)
    {
        return $user->hasRole('Gestionnaire') || $user->hasRole('Directeur');
    }

    // Voir une commande
    public function view(User $user, Commande $commande)
    {
        return $user->hasRole('Gestionnaire') || $user->hasRole('Directeur');
    }

    // Créer une commande
    public function create(User $user)
    {
        return $user->hasRole('Gestionnaire') || $user->hasRole('Directeur');
    }

    // Modifier une commande
    public function update(User $user, Commande $commande)
    {
        return $user->hasRole('Gestionnaire') || $user->hasRole('Directeur');
    }

    // Supprimer une commande
    public function delete(User $user, Commande $commande)
    {
        return $user->hasRole('Gestionnaire') || $user->hasRole('Directeur');
    }
}
