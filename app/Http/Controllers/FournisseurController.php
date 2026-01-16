<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Entree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $fournisseurs = Fournisseur::paginate(12);
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nom', 'marque', 'telephone']);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('fournisseurs', 'public');
            $data['photo'] = $path;
        }

        Fournisseur::create($data);
        
        return redirect()->route('suppliers.index')
                         ->with('success', 'Fournisseur ajouté avec succès');
    }

    /**
     * Afficher un fournisseur avec son historique
     */
  /**
 * Afficher un fournisseur avec son historique
 */
/**
 * Afficher un fournisseur avec son historique
 */
public function show(Fournisseur $supplier)
{
    // Charger les entrées avec leurs articles
    $entrees = Entree::where('fournisseur_id', $supplier->id)
                    ->with(['articles'])
                    ->orderBy('date_reception', 'desc')
                    ->paginate(10);
    
    // Calculer le total des achats depuis la table pivot article_entree
    $totalAchats = \DB::table('article_entree')
                    ->join('entrees', 'article_entree.entree_id', '=', 'entrees.id')
                    ->where('entrees.fournisseur_id', $supplier->id)
                    ->selectRaw('SUM(article_entree.quantite_total * article_entree.prix_unitaire) as total')
                    ->value('total') ?? 0;
    
    // Nombre total de bons d'achat
    $nombreBons = Entree::where('fournisseur_id', $supplier->id)->count();
    
    return view('fournisseurs.show', compact('supplier', 'entrees', 'totalAchats', 'nombreBons'));
}

    /**
     * Formulaire d'édition
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
            'marque' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_photo' => 'nullable|boolean'
        ]);

        $data = $request->only(['nom', 'marque', 'telephone']);

        // Supprimer la photo
        if ($request->has('remove_photo') && $request->remove_photo == 1) {
            if ($supplier->photo) {
                Storage::delete('public/' . $supplier->photo);
                $data['photo'] = null;
            }
        }

        // Upload nouvelle photo
        if ($request->hasFile('photo')) {
            if ($supplier->photo) {
                Storage::delete('public/' . $supplier->photo);
            }
            $path = $request->file('photo')->store('fournisseurs', 'public');
            $data['photo'] = $path;
        }

        $supplier->update($data);

        return redirect()->route('suppliers.index')
                         ->with('success', 'Fournisseur mis à jour avec succès');
    }

    /**
     * Supprimer un fournisseur
     */
    public function destroy(Fournisseur $supplier)
    {
        // Supprimer la photo si elle existe
        if ($supplier->photo) {
            Storage::delete('public/' . $supplier->photo);
        }
        
        $supplier->delete();
        
        return redirect()->route('suppliers.index')
                         ->with('success', 'Fournisseur supprimé avec succès');
    }
}