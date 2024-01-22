<?php

namespace App\Http\Controllers\api;

use App\Mail\info;
use App\Mail\infosNews;
use App\Models\newsLetter;
use App\Mail\NewsLetterMail;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use App\Mail\NewsInfos;
use Illuminate\Support\Facades\Mail;
use App\Mail\Newsletter as MailNewsletter;

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

class NewsLetterController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/newsletters",
     * tags={"NewsLetter"},
     *     summary="liste de tous les newsletters",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {
        $newsletters = newsLetter::all();
        return response()->json([
            "messages" => "Liste des newsletter",
            "datas" => $newsletters
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/newsletters",
     *     summary="Ajout d'un newsletter",
     *     tags={"NewsLetter"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="email", type="string", example="exemple@gmail.com"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="newsletter créée avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $newsLetter = new newsLetter();
        $newsLetter->email = $request->email;
        $newsLetter->save();
        return response()->json([
            'statut' => 'OK',
            'newsletter' => $newsLetter
        ]);
    }

    public function infoNews(Request $request)
    {
        $info = [];

        $titre = $request->titre;
        $contenu = $request->contenu;

        $info[] = $titre;
        $info[] = $contenu;

        $newsletters = newsLetter::all();
        foreach ($newsletters as $newsletter) {
            Mail::to($newsletter->email)->send(new NewsInfos($titre, $contenu));
        }
        return response()->json([
            "message" => "L'info",
            "datas" => $info
        ]);
    }


    /**
     * @OA\Delete(
     *     path="/api/newsletters/{newsLetter}",
     * tags={"NewsLetter"}, 
     *     summary="Supprimer un newsLetter",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="Newsletter",
     *         in="path",
     *         required=true,
     *         description="ID du newsletter à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(newsLetter $newsLetter)
    {
        $newsLetter->delete();
        return response()->json([
            'statut' => 'OK',
            'message' => 'Newsletter supprimer avec succès'
        ]);
    }
}
