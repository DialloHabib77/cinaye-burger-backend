<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PDF;

class CommandeController extends Controller
{
    public function index()
    {
        return Commande::with(['client', 'burger'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'burger_id' => 'required|exists:burgers,id',
        ]);

        $commande = Commande::create($request->all());
        return $commande->load(['client', 'burger']);
    }

    public function show(Commande $commande)
    {
        return $commande->load(['client', 'burger']);
    }

    public function update(Request $request, Commande $commande)
    {
        $request->validate([
            'etat' => 'required|in:en_cours,termine,paye,annule',
        ]);

        $commande->update($request->all());

        if ($request->etat == 'termine') {
            $this->envoyerFacture($commande);
        }

        return $commande;
    }

    public function destroy(Commande $commande)
    {
        $commande->delete();
        return response()->json(null, 204);
    }

    private function envoyerFacture(Commande $commande)
    {
        $pdf = PDF::loadView('factures.commande', ['commande' => $commande]);

        Mail::send('emails.facture', ['commande' => $commande], function($message) use ($commande, $pdf) {
            $message->to($commande->client->email)
                    ->subject('Votre commande est prête')
                    ->attachData($pdf->output(), "facture.pdf");
        });
    }

    public function filtrer(Request $request)
    {
        $query = Commande::query();

        if ($request->has('burger_id')) {
            $query->where('burger_id', $request->burger_id);
        }

        if ($request->has('date')) {
            $query->whereDate('date_commande', $request->date);
        }

        if ($request->has('etat')) {
            $query->where('etat', $request->etat);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        return $query->with(['client', 'burger'])->get();
    }

    public function statistiques()
    {
        $today = now()->toDateString();

        return [
            'commandes_en_cours' => Commande::where('etat', 'en_cours')->whereDate('date_commande', $today)->count(),
            'commandes_validees' => Commande::where('etat', 'termine')->whereDate('date_commande', $today)->count(),
            'recettes_journalieres' => Paiement::whereDate('date_paiement', $today)->sum('montant'),
            'commandes_annulees' => Commande::where('etat', 'annule')->whereDate('date_commande', $today)->count(),
        ];
    }
}