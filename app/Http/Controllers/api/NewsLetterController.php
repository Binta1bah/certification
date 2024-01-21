<?php

namespace App\Http\Controllers\api;

use App\Models\newsLetter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
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

    /**
     * Display the specified resource.
     */
    public function show(newsLetter $newsLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(newsLetter $newsLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, newsLetter $newsLetter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
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
