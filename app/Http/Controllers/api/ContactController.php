<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Contact;
use App\Mail\contactAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
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
     *     path="/api/contacts",
     *     summary="Contacter l'administrateur",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="nom", type="string", example="John"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="message", type="Mon message"),
     *         )
     *        )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact envoyé avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ -]+$/',
            'email' => 'required|regex:/^[a-zA-Z0-9]+@[a-z]+\.[a-z]{2,}$/',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            // Retourner les erreurs de validation
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        $contact = new Contact();
        $contact->nom = $request->nom;
        $contact->email = $request->email;
        $contact->message = $request->message;


        if ($contact->save()) {

            $user = User::where('role_id', 2)->first();

            Mail::to($user->email)->send(new contactAdmin($request->nom, $request->email, $request->message));

            return response()->json([
                'message' => 'Contact envoyé avec succés',
                'info' => $contact
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/contacts/{contact}",
     *     tags={"Contact"}, 
     *     summary="Supprimer un contact",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="contact",
     *         in="path",
     *         required=true,
     *         description="ID du contact à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json([
            'message' => 'Contact supprimée avec succés'
        ]);
    }
}
