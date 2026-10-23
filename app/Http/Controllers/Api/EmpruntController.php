<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Emprunt;
use Carbon\Carbon;
use App\Models\Parametre;
class EmpruntController extends Controller
{
    // Liste des emprunts (pour l'adhérent ou le bibliothécaire)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'BIBLIOTHECAIRE') {
            return response()->json(Emprunt::with(['user', 'livre'])->get());
        }

        return response()->json(Emprunt::with('livre')->where('user_id', $user->id)->get());
    }

    // Demande d'emprunt par l'adhérent (Max 5 actifs)
    public function store(Request $request)
    {
        $user = $request->user();

        // Vérification du quota max de 5 emprunts actifs
        $actifsCount = Emprunt::where('user_id', $user->id)
            ->whereIn('statut', ['EN_ATTENTE', 'EN_COURS', 'RETOUR_SIGNALE'])
            ->count();

        if ($actifsCount >= 5) {
            return response()->json([
                'message' => 'Quota atteint : vous ne pouvez pas dépasser 5 emprunts simultanés.'
            ], 422);
        }

        $validated = $request->validate([
            'livre_id' => 'required|exists:livres,id',
        ]);

        $emprunt = Emprunt::create([
            'user_id' => $user->id,
            'livre_id' => $validated['livre_id'],
            'statut' => 'EN_ATTENTE',
            'date_demande' => Carbon::now()->toDateString(),
        ]);

        return response()->json([
            'message' => 'Demande d\'emprunt enregistrée avec succès',
            'emprunt' => $emprunt
        ], 201);
    }

    // Validation du prêt par le bibliothécaire (calcule la date de retour prévue)
    public function validerPrêt($id)
    {
        $emprunt = Emprunt::findOrFail($id);
        
        // Récupérer la durée paramétrée (21 jours par défaut)
        $param = Parametre::where('cle', 'duree_pret_jours')->first();
        $duree = $param ? $param->valeur : 21;

        $emprunt->update([
            'statut' => 'EN_COURS',
            'date_retour_prevue' => Carbon::now()->addDays($duree)->toDateString(),
        ]);

        return response()->json(['message' => 'Prêt validé avec succès', 'emprunt' => $emprunt]);
    }

    // Refus de la demande par le bibliothécaire
    public function refuserPrêt($id)
    {
        $emprunt = Emprunt::findOrFail($id);
        $emprunt->update(['statut' => 'REFUSE']);

        return response()->json(['message' => 'Demande refusée']);
    }

    // Signalement de retour par l'adhérent
    public function signalerRetour($id)
    {
        $emprunt = Emprunt::findOrFail($id);
        $emprunt->update(['statut' => 'RETOUR_SIGNALE']);

        return response()->json(['message' => 'Retour signalé avec succès']);
    }

    // Validation effective du retour par le bibliothécaire
    public function validerRetour($id)
    {
        $emprunt = Emprunt::findOrFail($id);
        $emprunt->update([
            'statut' => 'RETOURNE',
            'date_retour_effective' => Carbon::now()->toDateString(),
        ]);

        return response()->json(['message' => 'Retour validé et livre libéré']);
    }
}
