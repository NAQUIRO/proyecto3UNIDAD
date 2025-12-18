<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class PatrocinadorController extends Controller
{
    /**
     * Display a listing of sponsors
     */
    public function index(Request $request)
    {
        $query = Sponsor::where('is_active', true)
            ->with('congress');

        // Filtrar por congreso si se especifica
        if ($request->has('congress') && $request->congress) {
            $query->where('congress_id', $request->congress);
        }

        // Filtrar por tipo
        if ($request->has('type') && $request->type) {
            $query->where('sponsor_type', $request->type);
        }

        // Buscar
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sponsors = $query->orderByRaw("FIELD(sponsor_type, 'platinum', 'gold', 'silver', 'bronze', 'partner')")
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Agrupar por tipo
        $sponsorsByType = $sponsors->groupBy('sponsor_type');

        // Obtener congresos para filtro
        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        // Tipos de patrocinadores
        $sponsorTypes = [
            'platinum' => 'Platino',
            'gold' => 'Oro',
            'silver' => 'Plata',
            'bronze' => 'Bronce',
            'partner' => 'Socio',
        ];

        return view('public.sponsors.index', compact('sponsors', 'sponsorsByType', 'congresses', 'sponsorTypes'));
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

        // Obtener otros patrocinadores del mismo tipo
        $relatedSponsors = Sponsor::where('is_active', true)
            ->where('id', '!=', $sponsor->id)
            ->where('sponsor_type', $sponsor->sponsor_type)
            ->limit(4)
            ->get();

        return view('public.sponsors.show', compact('sponsor', 'relatedSponsors'));
    }
}
