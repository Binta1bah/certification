<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Mail\VoteMail;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

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
class EvaluationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/votes/{user}",
     * tags={"Evaluation"},
     *     summary="Le nombre de vote d'un utilisateur donné",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function index(User $user)
    {
        $nombreVotes = Evaluation::where('user_id', $user->id)->count();
        return response()->json([
            'Message' => 'Nombre de votes de ' .  $user->name,
            'nombre de vote' => $nombreVotes
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
     *     path="/api/voter/{user}",
     *     summary="Voter pour un utilisateur",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     tags={"Evaluation"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=201,
     *         description="Vote effectué avec succées",
     *     ),
     *     @OA\Response(response=401, description="Validation Error")
     * )
     */
    public function store(Request $request, User $user)
    {
        $evaluation = new Evaluation();
        $evaluation->evaluation = 'vote';
        $evaluation->user_id = $user->id;

        $evaluation->save();

        Mail::to($user)->send(new VoteMail);

        return response()->json([
            'Message' => 'Vote effectué',
            'evaluation' => $evaluation,
            'user' => $evaluation->user->name
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluation $evaluation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evaluation $evaluation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evaluation $evaluation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluation $evaluation)
    {
        //
    }
}
