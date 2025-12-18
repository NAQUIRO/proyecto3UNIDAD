<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    /**
     * Display a listing of sponsors
     */
    public function index(Request $request)
    {
        $query = Sponsor::where('is_active', true);

        // Filtrar por congreso si se especifica
        if ($request->has('congress') && $request->congress) {
            $query->where('congress_id', $request->congress);
        }

        // Filtrar por tipo
        if ($request->has('type') && $request->type) {
            $query->where('sponsor_type', $request->type);
        }

        $sponsors = $query->orderByRaw("FIELD(sponsor_type, 'platinum', 'gold', 'silver', 'bronze', 'partner')")
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Agrupar por tipo
        $sponsorsByType = $sponsors->groupBy('sponsor_type');

        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('public.sponsors.index', compact('sponsors', 'sponsorsByType', 'congresses'));
    }

    /**
     * Display the specified sponsor
     */
    public function show(Sponsor $sponsor)
    {
        if (!$sponsor->is_active) {
            abort(404);
        }

        $sponsor->load('congress');

        return view('public.sponsors.show', compact('sponsor'));
    }
}
