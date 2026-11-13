<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    // Liste des sous-catégories
    public function index()
    {
        $subcategories = Subcategory::with('category')->get();
        return view('subcategories.index', compact('subcategories'));
    }

   public function create()
{
    return view('subcategories.create');
}
// Recherche de catégories (autocomplétion)
public function searchCategories(Request $request)
{
    $term = $request->get('q', '');

    $categories = Category::where('nom', 'like', "%{$term}%")
        ->orderBy('nom')
        ->limit(20)
        ->get(['id', 'nom']);

    return response()->json([
        'results' => $categories->map(fn ($c) => [
            'id'   => $c->id,
            'text' => $c->nom,
        ]),
    ]);
}
    // Enregistrer une nouvelle sous-catégorie
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id'
        ]);

        Subcategory::create($request->only('nom','category_id'));

        return redirect()->route('subcategories.index')
                         ->with('success','Sous‑catégorie créée avec succès');
    }
}
