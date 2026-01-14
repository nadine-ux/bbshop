<?php


namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // Liste des catégories
public function index()
{
    $categories = Category::whereNull('parent_id')->get();

    return view('categories.cards', [
        'title' => 'Catégories',
        'categories' => $categories
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
public function show(Category $category)
{
    // Charger sous-catégories + nombre d’articles
    $category->load('children', 'articles');

    // 🔹 CAS 1 : a des sous-catégories
    if ($category->children->count() > 0) {
        return view('categories.cards', [
            'title' => $category->nom,
            'categories' => $category->children
        ]);
    }

    // 🔹 CAS 2 : pas de sous-catégories → articles
    $articles = $category->articles()->paginate(12);

    return view('articles.cards', compact('category', 'articles'));
}

}
