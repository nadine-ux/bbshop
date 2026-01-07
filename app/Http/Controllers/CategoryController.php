<?php


namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Liste des catégories
    public function index()
    {
        // On récupère toutes les catégories avec leurs enfants
        $categories = Category::with('children')->whereNull('parent_id')->get();
        return view('categories.index', compact('categories'));
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
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        Category::create($request->only('nom','parent_id'));

        return redirect()->route('categories.index')->with('success','Catégorie créée avec succès');
    }
}
