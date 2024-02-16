<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Mail\VoteMail;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

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
        $voteur = auth()->user();
        $evaluation = new Evaluation();
        $evaluation->evaluation = 'vote';
        $evaluation->user_id = $user->id;
        $evaluation->voteur_id = $voteur->id;

        if ($user->id == $voteur->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas voter pour vous'
            ]);
        }

        $evaluation->save();

        Mail::to($user)->send(new VoteMail);

        return response()->json([
            'Message' => 'Vote effectué',
            'vote pour' => $evaluation->user->name,
            'voter par ' => $voteur->name
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
     * @OA\Delete(
     *     path="/api/vote/{evaluation}",
     *     tags={"Evaluation"}, 
     *     summary="Supprimer un vote",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *  @OA\Parameter(
     *         name="evaluation",
     *         in="path",
     *         required=true,
     *         description="ID du vote à supprimer",
     *         @OA\Schema(type="integer")
     * ),
     *     @OA\Response(response="200", description="succes")
     * )
     */
    public function destroy(Evaluation $evaluation)
    {
        $user = auth()->user();
        if ($evaluation->voteur_id == $user->id) {
            $evaluation->delete();
            return response()->json([
                'message' => 'Vote supprimé'
            ]);
        }
    }
}
