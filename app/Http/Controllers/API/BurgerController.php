<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Burger;
use Illuminate\Http\Request;

class BurgerController extends Controller
{
    public function index()
    {
        return Burger::where('archived', false)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'description' => 'required',
        ]);

        return Burger::create($request->all());
    }

    public function show(Burger $burger)
    {
        return $burger;
    }

    public function update(Request $request, Burger $burger)
    {
        $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'description' => 'required',
        ]);

        $burger->update($request->all());
        return $burger;
    }

    public function destroy(Burger $burger)
    {
        $burger->archived = true;
        $burger->save();
        return response()->json(null, 204);
    }
}