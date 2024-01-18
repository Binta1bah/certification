<?php

namespace App\Http\Controllers\api;

use App\Models\Localite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocaliteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $localites = Localite::all();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des Localités",
            'datas' => $localites
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string'
        ]);

        $localite = new Localite();
        $localite->nom = $request->nom;
        $localite->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Localité ajoutée avec succès',
            'Role' => $localite
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Localite $localite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Localite $localite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Localite $localite)
    {
        $request->validate([
            'nom' => 'required|string'
        ]);

        $localite->nom = $request->nom;

        $localite->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Localité modifiée avec succès',
            'Categorie' => $localite
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Localite $localite)
    {
        if ($localite->exists()) {
            //  dd($localite);
            $localite->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Localite supprimée avec succès"
            ]);
        } else {
            return response()->json([
                'message' => 'Localité introuvable'
            ]);
        }
    }
}
