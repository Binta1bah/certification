<?php

namespace App\Http\Controllers\api;

use App\Models\Image;
use App\Models\Annonce;
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
class AnnonceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/annonces",
     * tags={"Annonce"},
     *     summary="liste de toutes les annonces",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {

        $annonces = Annonce::where('statut', 1)->get();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des annonces",
            'datas' => $annonces
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
     *     path="/api/annonces",
     *     summary="Ajout d'une annonce",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Annonce"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="etat", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="categorie_id", type="int"),
     *             @OA\Property(property="localite_id", type="int"),
     *             @OA\Property(property="date_limite", type="date"),
     *             @OA\Property(property="image", type="string", format="binary", description="Fichier de photo"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="annonce ajoutée avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string',
            'description' => 'required|string',
            'etat' => 'required|in:Comme Neuf,Bon Etat,Etat Moyen,A Bricoler',
            'type' => 'required|in:Don,Echange',
            'categorie_id' => 'required|exists:categories,id',
            'localite_id' => 'required|exists:localites,id',
            'date_limite' => 'required|date',
            'image.*' => 'required|file'
        ]);

        $user = auth()->user();
        $annonce = new Annonce();
        $annonce->libelle = $request->libelle;
        $annonce->description = $request->description;
        $annonce->etat = $request->etat;
        $annonce->type = $request->type;
        $annonce->categorie_id = $request->categorie_id;
        $annonce->localite_id = $request->localite_id;
        $annonce->date_limite = $request->date_limite;
        $annonce->user_id = $user->id;
        $annonce->save();

        $imagesData = [];

        foreach ($request->file('image') as $image) {
            $images = new Image();
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('images');
            $image->move($imagePath, $imageName);
            $images->image = 'images/' . $imageName;
            $images->annonce_id = $annonce->id;
            $images->save();

            $imagesData[] = $images;
        }

        return response()->json([
            'statut' => 'OK',
            'Message' => 'Annonce ajoutée avec succès',
            'Annonce' => $annonce,
            'Images' => $imagesData
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/annonces/{annonce}",
     *     tags={"Annonce"},
     *     summary="Details d'une annonce",
     *     @OA\Parameter(
     *         name="annonce",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à afficher",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function show(Annonce $annonce)
    {
        if ($annonce->statut == 1) {
            $images = Image::where('annonce_id', $annonce->id)->get();
            return response()->json([
                "message" => "Les détails de l'article",
                "data" => $annonce,
                "images" => $images
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annonce $annonce)
    {
        //
    }


    /**
     * @OA\Put(
     *     path="/api/annonces/{annonce}",
     *     tags={"Annonce"},
     *     summary="Modification d'une annonce",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *      @OA\Parameter(
     *         name="annonce",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à modifier",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="etat", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="categorie_id", type="int"),
     *             @OA\Property(property="localite_id", type="int"),
     *             @OA\Property(property="date_limite", type="date"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="annonce modifiée avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function update(Request $request, Annonce $annonce)
    {
        $user = auth()->user();
        $request->validate([
            'libelle' => 'required|string',
            'description' => 'required|string',
            'etat' => 'required|in:Comme Neuf,Bon Etat,Etat Moyen,A Bricoler',
            'type' => 'required|in:Don,Echange',
            'categorie_id' => 'required|exists:categories,id',
            'localite_id' => 'required|exists:localites,id',
            'date_limite' => 'required|date',
        ]);

        $annonce->update([
            'libelle' => $request->libelle,
            'description' => $request->description,
            'etat' => $request->etat,
            'type' => $request->type,
            'categorie_id' => $request->categorie_id,
            'localite_id' => $request->localite_id,
            'date_limite' => $request->date_limite,
            'user_id' => $user->id
        ]);

        return response()->json([
            'statut' => 'OK',
            'Message' => 'Annonce mise à jour avec succès',
            'Annonce' => $annonce
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/annonce/{annonce}",
     *     tags={"Annonce"}, 
     *     summary="Supprimer une annonce",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="annonce",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Annonce $annonce)
    {
        $user = auth()->user();
        if ($annonce->user_id == $user->id) {
            $annonce->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Annonce supprimée avec succès"
            ]);
        } else {
            return response()->json([
                'message' => "Vous n'avez pas l'autorisation de supprimer cette annonce"
            ]);
        }
    }
}
