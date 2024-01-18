<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserController extends Controller
{


    public function index()
    {
        $users = User::where('role_id', 1)->get();
        return response()->json([
            'message' => 'La liste des utilisateurs',
            'Utilisateurs' => $users
        ]);
    }

    public function usersBloques()
    {
        $users = User::where('role_id', 1)->where('is_bloqued', 1)->get();
        return response()->json([
            'message' => 'La liste des utilisateurs bloqués',
            'Utilisateurs' => $users
        ]);
    }


    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
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
                "message" => "c'est bon",
                "data" => $user
            ]);
        }
    }


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


    public function update(Request $request)
    {
        try {
            $this->authorize('update', User::class);
            $user = auth()->user();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8',
                'photo' => 'required',
                'telephone' => 'required|string|max:9|regex:/^7[0-9]{8}$/|unique:users,telephone',
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


    public function bloquerUser(User $user)
    {
        $user->is_bloqued = 1;
        if ($user->save()) {
            return response()->json([
                'message' => 'Utilisateur bloqué avec succes'
            ]);
        }
    }

    public function debloquerUser(User $user)
    {
        $user->is_bloqued = 0;
        $user->save();
        return response()->json([
            'message' => 'Utilisateur debloqué avec succes'
        ]);
    }

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
}
