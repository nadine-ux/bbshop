<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = ['nom','marque','telephone','email','adresse'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function entrees()
    {
        return $this->hasMany(Entree::class);
    }
}
