<?php

namespace App\Http\Controllers\api;

use App\Models\Image;
use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     *     path="/api/images/{annonce}",
     *     summary="Ajout d'une image sur une annonce",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Image"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="file", type="string", format="binary", description="Fichier de photo"),
     *         )
     *        )
     *     ),
     *     @OA\Parameter(
     *         name="annonce",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=201,
     *         description="Image ajoutée avec succées",
     *     )
     *    )
     */
    public function store(Request $request, Annonce $annonce)
    {

        $user = auth()->user();

        $request->validate([
            'file' => 'required|file'
        ]);

        if ($annonce->user_id == $user->id) {
            $imagee = new Image();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('images');
                $file->move($filePath, $fileName);
                $imagee->image = 'images/' . $fileName;
                $imagee->annonce_id = $annonce->id;
            }
            $imagee->save();
            return response()->json([
                "message" => "Image ajouté avec succès pour l'annonce " . $annonce->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/images/{image}",
     * tags={"Image"}, 
     *     summary="Supprimer une image",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="image",
     *         in="path",
     *         required=true,
     *         description="ID de l'image à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Image $image)
    {
        $user = auth()->user();
        $annonce = $image->annonce;
        if ($annonce->user_id == $user->id) {
            $image->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "image supprimée avec succès"
            ]);
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette image"
            ]);
        }
    }
}
