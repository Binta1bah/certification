<?php

namespace App\Http\Controllers\api;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            "statut" => "OK",
            "message" => "Liste des rôles",
            'datas' => $roles
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
            'libelle' => 'required|string'
        ]);

        $role = new Role();
        $role->libelle = $request->libelle;
        $role->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Rôle ajouter avec succès',
            'Role' => $role
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'libelle' => 'required|string'
        ]);

        $role->libelle = $request->libelle;
        $role->save();
        return response()->json([
            'statut' => 'OK',
            'Message' => 'Rôle ajouter avec succès',
            'Role' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->exists()) {
            $role->delete();
            return response()->json([
                "statut" => "OK",
                "message" => "Rôle supprimer avec succès"
            ]);
        } else {
            return response()->json([
                'message' => 'Rôle introuvable'
            ]);
        }
    }
}
