<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\EntreeController;
use App\Http\Controllers\SortieController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\CommandeController;

Route::get('/', fn() => view('welcome'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/admin/reports', fn() => 'Rapports - Directeur')
    ->middleware('role:Directeur')->name('admin.reports');
Route::middleware(['auth','role:Employé'])->group(function () {
    Route::get('my-requests', [DemandeController::class, 'index'])->name('demandes.index');
    Route::get('my-requests/create', [DemandeController::class, 'create'])->name('demandes.create');
    Route::post('my-requests', [DemandeController::class, 'store'])->name('demandes.store');
});
// Gestionnaire & Directeur : accès complet aux commandes
Route::middleware(['auth','role:Gestionnaire|Directeur'])->group(function () {
    Route::resource('commandes', CommandeController::class);
});


Route::middleware(['auth','role:Gestionnaire'])->group(function () {
    Route::resource('demandes', DemandeController::class);
    Route::get('requests', [DemandeController::class, 'all'])->name('demandes.all'); // liste toutes les demandes
    Route::post('requests/{demande}/valider', [DemandeController::class, 'valider'])->name('demandes.valider');
    Route::post('requests/{demande}/refuser', [DemandeController::class, 'refuser'])->name('demandes.refuser');
  });
// web.php
Route::get('/notifications/{id}/read', function ($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return redirect()->route('demandes.show', $notification->data['demande_id']);
})->name('notifications.read');

Route::get('/stock', fn() => 'Module Stock - Gestionnaire & Directeur')
    ->middleware('permission:manage stock')->name('stock.index');

Route::middleware(['auth'])->group(function () {
    Route::resource('articles', ArticleController::class);
    Route::resource('suppliers', FournisseurController::class)
     ->middleware('permission:manage suppliers');
    Route::middleware(['auth','permission:manage stock'])->group(function () {
    Route::resource('entrees', EntreeController::class);
    Route::middleware(['auth','permission:manage stock'])->group(function () {
    Route::resource('sorties', SortieController::class);
});
Route::middleware(['auth','permission:manage stock'])->group(function () {
    Route::get('mouvements', [MouvementController::class,'index'])->name('mouvements.index');
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubcategoryController::class);
});
});


});

require __DIR__.'/auth.php';
