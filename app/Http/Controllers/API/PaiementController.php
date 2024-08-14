<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Commande;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index()
    {
        return Paiement::with('commande')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'montant' => 'required|numeric',
        ]);

        $commande = Commande::findOrFail($request->commande_id);

        if ($commande->paiement) {
            return response()->json(['message' => 'Cette commande a déjà été payée.'], 400);
        }

        $paiement = Paiement::create([
            'commande_id' => $request->commande_id,
            'montant' => $request->montant,
            'date_paiement' => now(),
        ]);

        $commande->update(['etat' => 'paye']);

        return $paiement->load('commande');
    }

    public function show(Paiement $paiement)
    {
        return $paiement->load('commande');
    }

    public function update(Request $request, Paiement $paiement)
    {
        $request->validate([
            'montant' => 'required|numeric',
        ]);

        $paiement->update($request->all());
        return $paiement;
    }

    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return response()->json(null, 204);
    }
}