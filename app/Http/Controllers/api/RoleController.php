<?php

namespace App\Http\Controllers\api;

use App\Models\Role;
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
class RoleController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/roles",
     * tags={"Role"},
     *     summary="liste de tous les rôles",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des rôles",
            'datas' => $roles
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
     *     path="/api/roles",
     *     summary="Ajout d'un rôle",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Role"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string", example="Objet"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rôle créé avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);

        $role = new Role();
        $role->libelle = $request->libelle;
        $role->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Rôle ajouter avec succès',
            'Role' => $role
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }





    /**
     * @OA\Put(
     *     path="/api/roles/{role}",
     *     tags={"Role"}, 
     *     summary="Modifier le libelle d'un rôle",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID du rôle à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="libelle", type="string", example="Nouveau Libelle"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Succès"),
     *     @OA\Response(response="401", description="Non autorisé"),
     *     @OA\Response(response="404", description="Rôle non trouvé"),
     *     @OA\Response(response="422", description="Erreur de validation")
     * )
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);

        $role->libelle = $request->libelle;
        $role->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Rôle ajouter avec succès',
            'Role' => $role
        ]);
    }



    /**
     * @OA\Delete(
     *     path="/api/roles/{role}",
     * tags={"Role"}, 
     *     summary="Supprimer un rôle",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID du rôle",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Role $role)
    {
        if ($role->exists()) {
            $role->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Rôle supprimer avec succès"
            ]);
        } else {
            return response()->json([
                'message' => 'Rôle introuvable'
            ]);
        }
    }
}
