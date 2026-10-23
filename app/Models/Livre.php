<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Emprunt;
class Livre extends Model
{
    protected $fillable = [
        'isbn',
        'titre',
        'auteur',
        'categorie',
        'actif',
    ];

    public function emprunts()
    {
        return $this->hasMany(Emprunt::class);
    }
}
