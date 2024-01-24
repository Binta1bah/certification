<?php

namespace App\Http\Controllers\api;

use App\Models\Article;
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
class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/articles",
     * tags={"Article"},
     *     summary="liste de tous les categories",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {
        $articles = Article::all();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des articles",
            'datas' => $articles
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/nombresArticles",
     * tags={"Article"},
     * security={
     *         {"bearerAuth": {}}
     *     },
     *     summary="Nombres d'articles",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function nombreArticles()
    {
        $nombreArticle = Article::count();

        return response()->json([
            'message' => 'Nombre d\'article',
            'nombre' => $nombreArticle
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
     *     path="/api/articles",
     *     summary="Ajout d'un article",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Article"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="image", type="string", format="binary", description="Fichier de photo"),
     *             @OA\Property(property="contenu", type="string"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article ajouté avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|string',
            'image' => 'required',
            'contenu' => 'required'
        ]);

        $article = new Article();
        $article->libelle = $request->libelle;
        $article->contenu = $request->contenu;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('images');
            $image->move($imagePath, $imageName);
            $article->image = 'images/' . $imageName;
        }
        $article->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Article ajouté avec succès',
            'Article' => $article
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/articles/{article}",
     *     tags={"Article"},
     *     summary="Details d'un article",
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article à afficher",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="image", type="string", example="path/to/photo.jpg"),
     *             @OA\Property(property="contenu", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function show(Article $article)
    {
        return response()->json([
            "message" => "Les détails de l'articles",
            "data" => $article
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }



    /**
     * @OA\Put(
     *     path="/api/articles/{article}",
     *     tags={"Article"},
     *     summary="Modification d'un article",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *      @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article à modifier",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="libelle", type="string"),
     *             @OA\Property(property="image", type="string", format="binary", description="Fichier de photo"),
     *             @OA\Property(property="contenu", type="string"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article modifié avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'libelle' => 'required|string',
            'contenu' => 'required'
        ]);

        $article->libelle = $request->libelle;
        $article->contenu = $request->contenu;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = public_path('images');
            $image->move($imagePath, $imageName);
            $article->image = 'images/' . $imageName;
        }

        $article->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Article modifié avec succès',
            'Article' => $article
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/articles/{article}",
     *     tags={"Article"}, 
     *     summary="Supprimer un article",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID de l'article à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Article $article)
    {
        if ($article->exists()) {
            $article->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Article supprimé avec succès"
            ]);
        } else {
            return response()->json([
                'message' => 'Article introuvable'
            ]);
        }
    }
}
