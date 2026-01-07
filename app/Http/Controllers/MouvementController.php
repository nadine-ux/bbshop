<?php

namespace App\Http\Controllers;

use App\Models\Entree;
use App\Models\Sortie;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MouvementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage stock');
    }

    public function index(Request $request)
    {
        $type = $request->get('type'); // 'Entrée' | 'Sortie' | null
        $date = $request->get('date'); // 'YYYY-MM-DD' or null

        // Build base queries
        $entreesQuery = Entree::with(['fournisseur','articles']);
        $sortiesQuery = Sortie::with(['articles']);

        // Apply type filter by skipping one set
        if ($type === 'Entrée') {
            $sortiesQuery = null;
        } elseif ($type === 'Sortie') {
            $entreesQuery = null;
        }

        // Apply date filter on each query using their actual date fields
        if ($date) {
            if ($entreesQuery) {
                $entreesQuery->whereDate('date_reception', $date);
            }
            if ($sortiesQuery) {
                // if you have an explicit date field, use it; otherwise fallback to created_at
                $sortiesQuery->whereDate('created_at', $date);
            }
        }

        // Fetch, normalize to a common shape
        $entrees = collect();
        if ($entreesQuery) {
            $entrees = $entreesQuery->get()->map(function ($e) {
                return [
                    'date'       => $e->date_reception,                 // normalized date
                    'type'       => 'Entrée',
                    'partenaire' => $e->fournisseur->nom ?? '—',
                    'articles'   => $e->articles->map(function ($a) {
                        $qt = $a->pivot->quantite_total;
                        $c  = $a->contenance_carton;
                        $cartons = intdiv($qt, $c);
                        $reste   = $qt % $c;
                        return $a->nom.' : '.$qt.' pièces ('.$cartons.' cartons, '.$reste.' pièces)';
                    })->implode(', ')
                ];
            });
        }

        $sorties = collect();
        if ($sortiesQuery) {
            $sorties = $sortiesQuery->get()->map(function ($s) {
                return [
                    'date'       => ($s->date_sortie ?? $s->created_at), // use explicit field if you have it
                    'type'       => 'Sortie',
                    'partenaire' => $s->destination ?? '—',
                    'articles'   => $s->articles->map(function ($a) {
                        $qt = $a->pivot->quantite_total;
                        $c  = $a->contenance_carton;
                        $cartons = intdiv($qt, $c);
                        $reste   = $qt % $c;
                        return $a->nom.' : '.$qt.' pièces ('.$cartons.' cartons, '.$reste.' pièces)';
                    })->implode(', ')
                ];
            });
        }

        // Merge and sort
        $merged = $entrees->merge($sorties)->sortByDesc(function ($m) {
            return \Carbon\Carbon::parse($m['date']);
        });

        // Paginate the merged collection
        $perPage = 10;
        $page    = LengthAwarePaginator::resolveCurrentPage();
        $slice   = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $mouvements = new LengthAwarePaginator(
            $slice,
            $merged->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('mouvements.index', compact('mouvements'));
    }
}
