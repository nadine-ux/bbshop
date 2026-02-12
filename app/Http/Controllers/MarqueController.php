<?php

namespace App\Http\Controllers;

use App\Models\Marque;
use Illuminate\Http\Request;

class MarqueController extends Controller
{
    /**
     * Afficher la liste des marques
     */
    public function index(Request $request)
    {
        $nom = $request->get('nom');

        $query = Marque::query();

        if ($nom) {
            $query->where('nom', 'like', "%{$nom}%");
        }

        $marques = $query->orderBy('nom', 'asc')->paginate(15)->appends($request->query());

        return view('marques.index', compact('marques'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('marques.create');
    }

    /**
     * Enregistrer une nouvelle marque
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:marques',
        ]);

        Marque::create($request->all());

        return redirect()->route('marques.index')->with('success', 'Marque créée avec succès');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Marque $marque)
    {
        return view('marques.edit', compact('marque'));
    }

    /**
     * Mettre à jour une marque
     */
    public function update(Request $request, Marque $marque)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:marques,nom,' . $marque->id,
        ]);

        $marque->update($request->all());

        return redirect()->route('marques.index')->with('success', 'Marque modifiée avec succès');
    }

    /**
     * Supprimer une marque
     */
    public function destroy(Marque $marque)
    {
        // Vérifier si la marque est utilisée par des articles
        if ($marque->articles()->count() > 0) {
            return redirect()->route('marques.index')->with('error', 'Impossible de supprimer cette marque car elle est utilisée par des articles');
        }

        $marque->delete();

        return redirect()->route('marques.index')->with('success', 'Marque supprimée avec succès');
    }
}