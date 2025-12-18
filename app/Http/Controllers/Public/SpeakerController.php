<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Speaker;
use Illuminate\Http\Request;

class SpeakerController extends Controller
{
    /**
     * Display a listing of speakers
     */
    public function index(Request $request)
    {
        $query = Speaker::where('is_active', true);

        // Filtrar por congreso si se especifica
        if ($request->has('congress') && $request->congress) {
            $query->where('congress_id', $request->congress);
        }

        // Buscar
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('institution', 'like', '%' . $request->search . '%')
                  ->orWhere('specialization', 'like', '%' . $request->search . '%');
            });
        }

        $speakers = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        $featuredSpeakers = Speaker::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('public.speakers.index', compact('speakers', 'featuredSpeakers', 'congresses'));
    }

    /**
     * Display the specified speaker
     */
    public function show(Speaker $speaker)
    {
        if (!$speaker->is_active) {
            abort(404);
        }

        $speaker->load('congress', 'papers');

        return view('public.speakers.show', compact('speaker'));
    }
}
