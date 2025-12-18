<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\ThematicArea;
use Illuminate\Http\Request;

class CongressController extends Controller
{
    /**
     * Display the home page.
     */
    public function home()
    {
        return view('public.home');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Congress::where('status', 'published')
            ->with('thematicAreas');

        // Filter by thematic area
        if ($request->has('thematic_area') && $request->thematic_area) {
            $query->whereHas('thematicAreas', function ($q) use ($request) {
                $q->where('thematic_areas.id', $request->thematic_area);
            });
        }

        // Filter by status (if needed for public view)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $congresses = $query->orderBy('start_date', 'desc')->paginate(12);
        $thematicAreas = ThematicArea::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.congresses.index', compact('congresses', 'thematicAreas'));
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $congress = Congress::where('slug', $slug)
            ->where('status', 'published')
            ->with('thematicAreas')
            ->firstOrFail();

        return view('public.congresses.show', compact('congress'));
    }
}
