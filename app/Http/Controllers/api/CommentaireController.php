<?php

namespace App\Http\Controllers\api;

use App\Models\Commentaire;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commentaires = Commentaire::all();
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
     * Store a newly created resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Commentaire $commentaire)
    {
        $user = auth()->user();
        if ($commentaire->user_id == $user->id || $user->role_id) {
            $commentaire->delete();
            return response()->json([
                'Statut' => 'OK',
                'message' => 'Commentaire supprimé avec succès'
            ]);
        }
    }
}
