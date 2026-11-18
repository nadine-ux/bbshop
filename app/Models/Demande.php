<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'employe_id', 'gestionnaire_id',
        'statut', 'remarque', 'motif_refus', 'date',
        'date_validation', 'date_reception',
    ];

    protected $casts = [
        'date_validation' => 'datetime',
        'date_reception'  => 'datetime',
    ];

    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    public function lignes()
    {
        return $this->hasMany(DemandeLigne::class);
    }
}