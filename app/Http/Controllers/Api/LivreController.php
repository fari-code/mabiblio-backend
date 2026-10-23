<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Livre;
class LivreController extends Controller
{
    // Liste publique des livres actifs
    public function index()
    {
        return response()->json(Livre::where('actif', true)->get());
    }

    // Ajout d'un livre (Bibliothécaire)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|unique:livres,isbn',
            'titre' => 'required|string',
            'auteur' => 'required|string',
            'categorie' => 'required|string',
        ]);

        $livre = Livre::create($validated);

        return response()->json([
            'message' => 'Livre ajouté avec succès au catalogue',
            'livre' => $livre
        ], 201);
    }

    // Désactivation logique d'un livre
    public function destroy($id)
    {
        $livre = Livre::findOrFail($id);
        $livre->update(['actif' => false]);

        return response()->json(['message' => 'Livre désactivé avec succès']);
    }
}
