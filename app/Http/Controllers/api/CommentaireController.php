<?php

namespace App\Http\Controllers\api;

use App\Models\Commentaire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use OpenApi\Annotations as OA;

class CommentaireController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/commentaires/{article}",
     * tags={"Commentaire"},
     *     summary="liste de tous les commentaires",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index(Article $article)
    {
        $commentaires = Commentaire::where('article_id', $article->id)->get();
        return response()->json([
            'message' => 'liste des commentaires',
            'Commentaires' => $commentaires
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
     *     path="/api/commentaires/{article}",
     *     summary="Ajout d'un commentaire",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Commentaire"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="commentaire", type="string", example="commentaire"),
     *         )
     *        )
     *     ),
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article à commenter",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=201,
     *         description="Commentaire ajouté avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request, Article $article)
    {
        $user = auth()->user();
        $request->validate([
            'commentaire' => 'required|string'
        ]);

        $commentaire = new Commentaire();
        $commentaire->commentaire = $request->commentaire;
        $commentaire->user_id = $user->id;
        $commentaire->article_id = $article->id;
        $commentaire->save();
        return response()->json([
            'Statut' => 'OK',
            'Commentaire' => $commentaire
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/commentaires/{commentaire}",
     * tags={"Commentaire"}, 
     *     summary="Supprimer un commentaire",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="commentaire",
     *         in="path",
     *         required=true,
     *         description="ID du commentaire à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Commentaire $commentaire)
    {
        $user = auth()->user();
        if ($commentaire->user_id == $user->id || $user->role_id == 2) {
            $commentaire->delete();
            return response()->json([
                'Statut' => 'OK',
                'message' => 'Commentaire supprimé avec succès'
            ]);
        }
    }
}
