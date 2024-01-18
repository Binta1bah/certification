<?php

namespace App\Http\Controllers\api;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Categorie::all();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des categories",
            'datas' => $categories
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
            'libelle' => 'required|string'
        ]);

        $categorie = new Categorie();
        $categorie->libelle = $request->libelle;
        $categorie->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Categorie ajoutée avec succès',
            'Role' => $categorie
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);

        $categorie->libelle = $request->libelle;

        $categorie->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Categorie modifiée avec succès',
            'Categorie' => $categorie
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
    {
        if ($categorie->exists()) {
            //  dd($categorie);
            $categorie->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Categorie supprimée avec succès"
            ]);
        } else {
            return response()->json([
                'message' => 'Categorie introuvable'
            ]);
        }
    }
}
