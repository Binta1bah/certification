<?php

namespace App\Http\Controllers\api;

use App\Models\Localite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="EcoLoop",
 *     version="1.0.0",
 *     description="Application de dons et d'échanges d'objets durables"
 * )
 */

/**
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */
class LocaliteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/localites",
     * tags={"Localite"},
     *     summary="liste de toutes les localités",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
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
     * @OA\Post(
     *     path="/api/localites",
     *     summary="Ajout d'une localité",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Localite"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="nom", type="string", example="nom"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Localité créé avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
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
     * @OA\Put(
     *     path="/api/localites/{localite}",
     *     tags={"Localite"}, 
     *     summary="Modifier le nom d'une localité",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="localite",
     *         in="path",
     *         required=true,
     *         description="ID de le localite à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property=" nom", type="string", example="Nouveau nom"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succès"),
     *     @OA\Response(response="401", description="Non autorisé"),
     *     @OA\Response(response="404", description="Rôle non trouvé"),
     *     @OA\Response(response="422", description="Erreur de validation")
     * )
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
     * @OA\Delete(
     *     path="/api/localites/{localite}",
     * tags={"Localite"}, 
     *     summary="Supprimer une localité",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="localite",
     *         in="path",
     *         required=true,
     *         description="ID de le localité",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
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
