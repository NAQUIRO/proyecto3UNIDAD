<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Speaker;
use Illuminate\Http\Request;

class PonenteController extends Controller
{
    /**
     * Display a listing of speakers
     */
    public function index(Request $request)
    {
        $query = Speaker::where('is_active', true)
            ->with('congress');

        // Filtrar por congreso si se especifica
        if ($request->has('congress') && $request->congress) {
            $query->where('congress_id', $request->congress);
        }

        // Buscar
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('institution', 'like', '%' . $request->search . '%')
                  ->orWhere('specialization', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%');
            });
        }

        // Filtrar por especialización
        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // Obtener ponentes destacados
        $featuredSpeakers = Speaker::where('is_active', true)
            ->where('is_featured', true)
            ->with('congress')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Obtener todos los ponentes sin paginación (como en el proyecto de referencia)
        $speakers = $query->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Obtener congresos para filtro
        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        // Obtener especializaciones únicas para filtro
        $specializations = Speaker::where('is_active', true)
            ->whereNotNull('specialization')
            ->distinct()
            ->pluck('specialization')
            ->filter()
            ->sort()
            ->values();

        return view('public.ponentes.index', compact('speakers', 'featuredSpeakers', 'congresses', 'specializations'));
    }

    /**
     * Display the specified speaker
     */
    public function show(Speaker $speaker)
    {
        if (!$speaker->is_active) {
            abort(404);
        }

        $speaker->load(['congress', 'papers' => function ($query) {
            $query->where('status', 'accepted')
                  ->orderBy('created_at', 'desc');
        }]);

        // Obtener otros ponentes relacionados
        $relatedSpeakers = Speaker::where('is_active', true)
            ->where('id', '!=', $speaker->id)
            ->where(function ($query) use ($speaker) {
                $query->where('specialization', $speaker->specialization)
                      ->orWhere('congress_id', $speaker->congress_id);
            })
            ->limit(4)
            ->get();

        return view('public.ponentes.show', compact('speaker', 'relatedSpeakers'));
    }
}
