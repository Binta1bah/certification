<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Image;
use App\Models\Annonce;
use App\Models\Localite;
use App\Models\Categorie;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Mail\NouvelleAnnonceMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AnnonceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/annonces",
     * tags={"Annonce"},
     *     summary="liste de toutes les annonces disponibles",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index()
    {
        $annonces = Annonce::where('statut', 1)->get();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des annonces disponibles",
            'datas' => $annonces
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/annonces/admin",
     * tags={"Annonce"},
     * security={
     *         {"bearerAuth": {}}
     *     },
     *     summary="liste de toutes les annonces disponibles et non disponibles",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function annoncesAdmin()
    {

        $annoncesDisponibles = Annonce::where('statut', 1)->get();
        $annoncesNonDisponibles = Annonce::where('statut', 0)->get();
        return response()->json([
            "statut" => "OK",
            "Annonces Disponibles" => $annoncesDisponibles,
            'Annonces non Disponibles' => $annoncesNonDisponibles
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/annonces/{user}",
     * tags={"Annonce"},
     * security={
     *         {"bearerAuth": {}}
     *     },
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     summary="les annonces d'un utilisateur",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function annoncesUser()
    {
        $user = auth()->user();
        $annonces = Annonce::where('user_id', $user->id)->get();
        return response()->json([
            "message" => "liste des annonces d'un utilisateur",
            "datas" => $annonces
        ]);
    }




    /**
     * @OA\Get(
     *     path="/api/etatAnnonce",
     * tags={"Annonce"},
     *     summary="les etats des annonces",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function etatAnnonce()
    {
        $etats = ['Comme Neuf', 'Bon Etat', 'Etat Moyen', 'A Bricoler'];
        return response()->json([
            'Etats' => $etats
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/typeAnnonce",
     * tags={"Annonce"},
     *     summary="les types d'annonces",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function typeAnnonce()
    {
        $types = ['Don', 'Echange'];
        return response()->json([
            'Types' => $types
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/nombresAnnonces",
     * tags={"Annonce"},
     * security={
     *         {"bearerAuth": {}}
     *     },
     *     summary="Nombres d'annonces",
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function nombreAnnonces()
    {
        $nombreAnnonces = Annonce::count();

        return response()->json([
            'message' => 'Nombre d\'annonces',
            'nombre' => $nombreAnnonces
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
     *            @OA\Property(property="image[]",type="array",@OA\Items(type="string", format="binary"),description="Liste de fichiers images")
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
        if ($annonce->save()) {

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

            $users = User::where('role_id', 1)->get();
            foreach ($users as $user) {
                Mail::to($user)->send(new NouvelleAnnonceMail);
            }

            return response()->json([
                'statut' => 'OK',
                'Message' => 'Annonce ajoutée avec succès',
                'Annonce' => $annonce,
                'Images' => $imagesData
            ]);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/detailsAnnonce/{annonce}",
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

        $images = Image::where('annonce_id', $annonce->id)->get();
        $nombreVotes = Evaluation::where('user_id', $annonce->user_id)->count();
        return response()->json([
            "message" => "Les détails de l'annonce",
            "annonce" => [
                'nom' => $annonce->libelle,
                'description' => $annonce->description,
                'etat' => $annonce->etat,
                'type' => $annonce->type,
                'categorie' => $annonce->categorie->libelle,
                'localité' => $annonce->localite->nom,
                'date_limite' => $annonce->date_limite,

            ],
            "images" => $images,
            "user" => [
                'nom' => $annonce->user->name,
                'photo' => $annonce->user->photo,
                'nombreVote' => $nombreVotes
            ]
        ]);
    }


    /**
     * @OA\Put(
     *     path="/api/cloturer/{annonce}",
     *     tags={"Annonce"},
     *     summary="Cloturer une annonce",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="annonce",
     *         in="path",
     *         required=true,
     *         description="ID de l'annonce à cloturer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function edit(Annonce $annonce)
    {
        $user = auth()->user();
        if ($user->id == $annonce->user_id) {
            if ($annonce->statut == 1) {
                $annonce->statut = 0;
                $annonce->save();
                return response()->json([
                    'message' => 'Annonce cloturée avec succés'
                ]);
            } else {
                return response()->json([
                    'message' => 'Cette annonce est dèjà cloturée'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas l\'autorisation de clo cette annonce'
            ]);
        }
    }


    /**
     * @OA\Post(
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
     * @OA\Get(
     *     path="/api/annoncesParCategorie/{categorie}",
     *     tags={"Annonce"},
     *     summary="Annonces par categorie",
     *     @OA\Parameter(
     *         name="categorie",
     *         in="path",
     *         required=true,
     *         description="ID de la categorie",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function annoncesParCategorie(Categorie $categorie)
    {
        // Récupérer la catégorie en fonction de l'id
        $categorie = Categorie::where('id', $categorie->id)->first();

        if ($categorie) {
            // Récupérer les annonces liées à la catégorie
            $annonces = Annonce::where('categorie_id', $categorie->id)->get();

            return response()->json([
                'statut' => 'OK',
                'annonces' => $annonces,
            ]);
        } else {
            return response()->json([
                'statut' => 'Erreur',
                'message' => 'Catégorie non trouvée',
            ], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/annoncesParLocalite/{localite}",
     *     tags={"Annonce"},
     *     summary="Annonces par localite",
     *     @OA\Parameter(
     *         name="localite",
     *         in="path",
     *         required=true,
     *         description="ID de la localite",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function annoncesParLocalite(Localite $localite)
    {
        // Récupérer l'annonce en fonction de l'id
        $localite = Localite::where('id', $localite->id)->first();

        if ($localite) {
            // Récupérer les annonces liées à la localité
            $annonces = Annonce::where('localite_id', $localite->id)->get();

            return response()->json([
                'statut' => 'OK',
                'annonces' => $annonces,
            ]);
        } else {
            return response()->json([
                'statut' => 'Erreur',
                'message' => 'Catégorie non trouvée',
            ], 404);
        }
    }



    /**
     * @OA\Get(
     *     path="/api/annoncesParType/{type}",
     *     tags={"Annonce"},
     *     summary="Annonces par Type",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="type d'annonce entre Don ou Echange",
     *         @OA\Schema(type="string")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */
    public function annoncesParType($type)
    {
        $annonces = Annonce::where('type', $type)->get();

        return response()->json([
            'statut' => 'OK',
            'annonces' => $annonces,
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
        if ($annonce->user_id == $user->id || $user->role_id == 2) {
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
