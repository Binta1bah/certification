<?php

namespace App\Http\Controllers\api;

use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

class CategorieController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/categories",
     * tags={"Categorie"},
     *     summary="liste de toutes les categories",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
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
     * @OA\Get(
     *     path="/api/nombreCategories",
     * tags={"Categorie"},
     * security={
     *         {"bearerAuth": {}}
     *     },
     *     summary="Nombres de categories",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function nombreCategories()
    {
        $nombreCategorie = Categorie::count();

        return response()->json([
            'message' => 'Nombre de categories',
            'nombre' => $nombreCategorie
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
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Ajout d'une categorie",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Categorie"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string", example="libelle"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categorie créée avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
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
            'Categorie' => $categorie
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
     * @OA\put(
     *     path="/api/categories/{categorie}",
     *     tags={"Categorie"}, 
     *     summary="Modifier la categorie",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="Categorie",
     *         in="path",
     *         required=true,
     *         description="ID de la categorie à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property=" libelle", type="string", example="Nouveau libelle"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succès"),
     * )
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
     * @OA\Delete(
     *     path="/api/categories/{categorie}",
     * tags={"Categorie"}, 
     *     summary="Supprimer une categorie",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="Categorie",
     *         in="path",
     *         required=true,
     *         description="ID de la categorie à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
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
