<?php


namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
public function index(Request $request)
{
    $query = Category::whereNull('parent_id')->withCount('children');

    // Recherche par nom
    if ($request->filled('search')) {
        $query->where('nom', 'like', '%' . $request->search . '%');
    }

    // Tri
    $sort      = $request->get('sort', 'nom');
    $direction = $request->get('direction', 'asc');
    $allowed   = ['nom', 'created_at'];

    if (in_array($sort, $allowed)) {
        $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
    }

    $categories = $query->get();

    return view('categories.cards', [
        'title'      => 'Catégories',
        'categories' => $categories,
    ]);
}

    // Formulaire de création
    public function create()
    {
        // On récupère toutes les catégories pour proposer un parent
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    // Enregistrer une nouvelle catégorie
    public function store(Request $request)
{
    $request->validate([
        'nom' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $data = $request->only('nom', 'parent_id');

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('categories', 'public');
    }

    Category::create($data);

    return redirect()->route('categories.index')
        ->with('success', 'Catégorie créée avec succès');
}
public function show(Category $category, Request $request)
{
    // Charger sous-catégories
    $category->load('children');

    // 🔹 CAS 1 : a des sous-catégories
    if ($category->children->count() > 0) {
        return view('categories.cards', [
            'title' => $category->nom,
            'categories' => $category->children
        ]);
    }

    // 🔹 CAS 2 : pas de sous-catégories → afficher les articles avec filtres
    
    // Construire la requête de base
    $query = $category->articles();

    // ✅ Filtre par marque
    if ($request->has('marque_id') && $request->marque_id != '') {
        $query->where('marque_id', $request->marque_id);
    }

    // ✅ Filtre par stock
    if ($request->has('stock_status')) {
        if ($request->stock_status == 'low') {
            $query->whereRaw('stock <= quantite_minimale');
        } elseif ($request->stock_status == 'ok') {
            $query->whereRaw('stock > quantite_minimale');
        }
    }

    // ✅ Filtre par prix
    if ($request->has('prix_min') && $request->prix_min != '') {
        $query->where('prix_vente', '>=', $request->prix_min);
    }
    if ($request->has('prix_max') && $request->prix_max != '') {
        $query->where('prix_vente', '<=', $request->prix_max);
    }

    // ✅ Recherche par nom
    if ($request->has('search') && $request->search != '') {
        $query->where('nom', 'like', '%' . $request->search . '%');
    }

    // ✅ Tri
    $sort = $request->get('sort', 'recent');
    switch($sort) {
        case 'name_asc':
            $query->orderBy('nom', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('nom', 'desc');
            break;
        case 'price_asc':
            $query->orderBy('prix_vente', 'asc');
            break;
        case 'price_desc':
            $query->orderBy('prix_vente', 'desc');
            break;
        case 'stock_asc':
            $query->orderBy('stock', 'asc');
            break;
        case 'stock_desc':
            $query->orderBy('stock', 'desc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }

    // Paginer les résultats et conserver les paramètres de filtre
    $articles = $query->paginate(12)->appends($request->query());
    
    // Charger les marques pour le filtre
    $marques = \App\Models\Marque::orderBy('nom')->get();

    return view('articles.cards', compact('category', 'articles', 'marques'));
}
 // 🔹 Editer une catégorie
    public function edit(Category $category)
    {
        // Charger toutes les catégories pour le select parent
        $categories = Category::where('id', '!=', $category->id)->get();

        return view('categories.edit', compact('category', 'categories'));
    }

    // 🔹 Mettre à jour la catégorie
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'photo' => 'nullable|image|max:2048' // si tu veux permettre la photo
        ]);

        $data = $request->only('nom','parent_id');

        // 🔹 Gestion photo si upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('categories','public');
            $data['photo'] = $path;
        }

        $category->update($data);

        return redirect()->route('categories.index')->with('success','Catégorie modifiée avec succès');
    }

    // 🔹 Supprimer une catégorie
    public function destroy(Category $category)
    {
        // Optionnel : vérifier qu’il n’y a pas d’articles ou de sous-catégories
        if ($category->children()->count() > 0 || $category->articles()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer : la catégorie contient des sous-catégories ou des articles.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success','Catégorie supprimée avec succès');
    }

}
