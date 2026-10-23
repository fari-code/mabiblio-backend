<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Livre;
class Emprunt extends Model
{
    protected $fillable = [
        'user_id',
        'livre_id',
        'statut',
        'date_demande',
        'date_retour_prevue',
        'date_retour_effective',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
