<?php

namespace App\Http\Controllers\api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
            'Role' => $article
        ]);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
            'Role' => $article
        ]);
    }

    /**
     * Remove the specified resource from storage.
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
