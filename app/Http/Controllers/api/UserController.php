<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
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


    
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'Déconnexion effectuée',
        ]);
    }



    protected function respondWithToken($token)
    {
        $user = auth()->user();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => auth()->factory()->getTTL() * 60
            'user' => $user
        ]);
    }
}
