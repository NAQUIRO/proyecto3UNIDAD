<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Symposium;
use App\Models\ThematicArea;
use Illuminate\Http\Request;

class SymposiumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Congress $congress)
    {
        $symposia = $congress->symposia()
            ->with('thematicArea', 'sessions')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->get();

        return view('congress.symposia.index', compact('congress', 'symposia'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        // Solo admins pueden crear simposios
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $thematicAreas = $congress->thematicAreas()->where('is_active', true)->get();

        return view('congress.symposia.create', compact('congress', 'thematicAreas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        // Solo admins pueden crear simposios
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thematic_area_id' => 'nullable|exists:thematic_areas,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'moderator_name' => 'nullable|string|max:255',
            'moderator_email' => 'nullable|email|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['is_active'] = $request->has('is_active');

        Symposium::create($validated);

        return redirect()->route('congress.symposia.index', $congress)
            ->with('success', 'Simposio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress, Symposium $symposium)
    {
        $symposium->load(['sessions.paper.author', 'thematicArea']);

        return view('congress.symposia.show', compact('congress', 'symposium'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, Symposium $symposium)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $thematicAreas = $congress->thematicAreas()->where('is_active', true)->get();

        return view('congress.symposia.edit', compact('congress', 'symposium', 'thematicAreas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, Symposium $symposium)
    {
        // Solo admins pueden editar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thematic_area_id' => 'nullable|exists:thematic_areas,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'moderator_name' => 'nullable|string|max:255',
            'moderator_email' => 'nullable|email|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $symposium->update($validated);

        return redirect()->route('congress.symposia.show', [$congress, $symposium])
            ->with('success', 'Simposio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, Symposium $symposium)
    {
        // Solo admins pueden eliminar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $symposium->delete();

        return redirect()->route('congress.symposia.index', $congress)
            ->with('success', 'Simposio eliminado exitosamente.');
    }
}
