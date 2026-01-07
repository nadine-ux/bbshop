<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function __construct()
    {
        // Seuls Directeur et Gestionnaire peuvent gérer les fournisseurs
        $this->middleware('permission:manage suppliers')->except(['index','show']);
    }

    /**
     * Liste des fournisseurs
     */
    public function index()
    {
        $fournisseurs = Fournisseur::paginate(10);
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('fournisseurs.create');
    }

    /**
     * Enregistrer un fournisseur
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'marque' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string',
        ]);

        Fournisseur::create($request->all());
        return redirect()->route('suppliers.index')->with('success','Fournisseur ajouté avec succès');
    }

    /**
     * Afficher un fournisseur
     */
    public function show(Fournisseur $supplier)
    {
        return view('fournisseurs.show', compact('supplier'));
    }

    /**
     * Formulaire d’édition
     */
    public function edit(Fournisseur $supplier)
    {
        return view('fournisseurs.edit', compact('supplier'));
    }

    /**
     * Mettre à jour un fournisseur
     */
    public function update(Request $request, Fournisseur $supplier)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success','Fournisseur mis à jour');
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy(Fournisseur $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success','Fournisseur supprimé');
    }
}
