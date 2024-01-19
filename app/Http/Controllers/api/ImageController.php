<?php

namespace App\Http\Controllers\api;

use App\Models\Image;
use App\Models\Annonce;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Annonce $annonce)
    {
        $user = auth()->user();
        $request->validate([
            'file' => 'required|file'
        ]);
        if ($annonce->user_id == $user->id) {
            $imagee = new Image();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('images');
                $file->move($filePath, $fileName);
                $imagee->image = 'images/' . $fileName;
                $imagee->annonce_id = $annonce->id;
            }
            $imagee->save();
            return response()->json([
                "message" => "Image ajouté avec succès pour l'annonce " . $annonce->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image, Annonce $annonce)
    {
        $user = auth()->user();
        if ($annonce->user_id == $user->id) {
            $image->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "image supprimée avec succès"
            ]);
        } else {
            return response()->json([
                'message' => "Vous n'avez pas le droit de supprimer cette image"
            ]);
        }
    }
}
