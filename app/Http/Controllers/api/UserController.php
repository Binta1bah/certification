<?php

namespace App\Http\Controllers\api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/users",
     * tags={"User"},
     *     summary="liste de tous les utilisateurs",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function index()
    {
        $users = User::where('role_id', 1)->get();
        return response()->json([
            'message' => 'La liste des utilisateurs',
            'Utilisateurs' => $users
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/usersBloques",
     * tags={"User"},
     *     summary="liste de tous les utilisateurs bloqués",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function usersBloques()
    {
        $users = User::where('role_id', 1)->where('is_bloqued', 1)->get();
        return response()->json([
            'message' => 'La liste des utilisateurs bloqués',
            'Utilisateurs' => $users
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/inscription",
     *     summary="Inscription d'un utilisateurs",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="telephone", type="string", example="784741478"),
     *             @OA\Property(property="photo", type="string", format="binary", description="Fichier de photo"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créer avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email|regex:/^[a-zA-Z0-9]+@[a-z]+\.[a-z]{2,}$/',
            'password' => 'required|string|min:8',
            'photo' => 'required',
            'telephone' => 'required|string|max:9|regex:/^7[0-9]{8}$/|unique:users,telephone',

        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->telephone = $request->telephone;
        $user->role_id = 1;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photoPath = public_path('images');
            $photo->move($photoPath, $photoName);
            $user->photo = 'images/' . $photoName;
        }

        if ($user->save()) {
            return response()->json([
                "status" => "ok",
                "message" => "Inscription effectuée avec succes",
                "data" => $user
            ]);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"User"},
     *     summary="Connexion d'un utilisateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email", example="bamagid@gmail.com"),
     *                 @OA\Property(property="password", type="string", format="password", example="11111111")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *     ),
     *     @OA\Response(response=401, description="Échec de l'authentification")
     * )
     */

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $credentials = request(['email', 'password']);


        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Connexion echoué; Entrez des information correctes'
            ], 401);
        }
        return $this->respondWithToken($token);
    }


    /**
     * @OA\get(
     *     path="/api/refresh",
     * tags={"User"},
     *     summary="raffraichir le token d'un utilisateur",
     *  security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function refresh()
    {
        try {
            $this->authorize('refresh', User::class);
            return $this->respondWithToken(auth()->refresh());
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Desolé, Vous ne pouvez pas effectuer cette tâche pour le moment.'
            ], 403);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/profil",
     *     tags={"User"},
     *     summary="Informations de profil d'un utilisateur",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="telephone", type="string", example="784741478"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
     *             @OA\Property(property="role", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non autorisé"),
     * )
     */

    public function profil()
    {
        try {
            $this->authorize('profil', User::class);
            $user = auth()->user();
            return response()->json([
                'message' => 'Vos information',
                'infos' => [
                    'Nom' => $user->name,
                    'Telephone' => $user->telephone,
                    'email' => $user->email,
                    'Photo' => $user->photo,
                    'Mot de passe' => $user->password
                ]
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Desolé, Vous ne pouvez pas effectuer cette tâche pour le moment.'
            ], 403);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/update",
     *     summary="Modification des informations d'un utilisateur",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="telephone", type="string", example="784741478"),
     *             @OA\Property(property="photo", type="string", format="binary", description="Fichier de photo"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créer avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function update(Request $request)
    {
        try {
            $this->authorize('update', User::class);
            $user = auth()->user();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|unique:users,email|regex:/^[a-zA-Z0-9]+@[a-z]+\.[a-z]{2,}$/' . auth()->id(),
                'password' => 'required|string|min:8',
                'telephone' => 'required|string|max:9|regex:/^7[0-9]{8}$/|unique:users,telephone,' . auth()->id(),
            ]);



            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->telephone = $request->telephone;

            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '.' . $photo->getClientOriginalExtension();
                $photoPath = public_path('images');
                $photo->move($photoPath, $photoName);
                $user->photo = 'images/' . $photoName;
            }

            if ($user->save()) {
                return response()->json([
                    "statut" => "ok",
                    "message" => "Modification effectuée",
                    "data" => $user
                ]);
            }
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Desolé, Vous ne pouvez pas effectuer cette tâche pour le moment.'
            ], 403);
        }
    }


    /**
     * @OA\put(
     *     path="/api/bloquer/{user}",
     * tags={"User"}, 
     *     summary="Bloquer un utilisateur par l'admin",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function bloquerUser(User $user)
    {
        $user->is_bloqued = 1;
        if ($user->save()) {
            return response()->json([
                'message' => 'Utilisateur bloqué avec succes'
            ]);
        }
    }


    /**
     * @OA\put(
     *     path="/api/debloquer/{user}",
     * tags={"User"}, 
     *     summary="Debloquer un utilisateur par l'admin",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function debloquerUser(User $user)
    {
        $user->is_bloqued = 0;
        $user->save();
        return response()->json([
            'message' => 'Utilisateur debloqué avec succes'
        ]);
    }


    /**
     * @OA\get(
     *     path="/api/logout",
     * tags={"User"},
     *     summary="Deconnexion d'un utilisateur",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'Déconnexion effectuée',
        ]);
    }


    protected function respondWithToken($token)
    {
        try {
            $this->authorize('login', User::class);

            $user = auth()->user();

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Desolé, Vous ne pouvez pas vous connecter pour le moment.'
            ], 403);
        }
    }


    /**
     * @OA\post(
     *     path="/api/users/whatsapp/{id}",
     * tags={"User"}, 
     *     summary="Contacter un utilisateur via whatsApp",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */

    public function redirigerWhatsApp($id)

    {
        try {
            // Validation de l'ID comme étant numérique
            if (!is_numeric($id)) {
                throw new Exception('L\'ID doit être numérique.');
            }
            $user = User::findOrFail($id);
            $numeroWhatsApp = $user->telephone;
            // dd($numeroWhatsApp);
            $urlWhatsApp = "https://api.whatsapp.com/send?phone=$numeroWhatsApp";

            return redirect()->to($urlWhatsApp);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
