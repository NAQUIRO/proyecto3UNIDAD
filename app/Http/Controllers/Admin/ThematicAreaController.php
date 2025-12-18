<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThematicArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThematicAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $thematicAreas = ThematicArea::orderBy('sort_order')->orderBy('name')->paginate(15);
        return view('admin.thematic-areas.index', compact('thematicAreas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.thematic-areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:thematic_areas,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        ThematicArea::create($validated);

        return redirect()->route('admin.thematic-areas.index')
            ->with('success', 'Área temática creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ThematicArea $thematicArea)
    {
        return view('admin.thematic-areas.show', compact('thematicArea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThematicArea $thematicArea)
    {
        return view('admin.thematic-areas.edit', compact('thematicArea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThematicArea $thematicArea)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:thematic_areas,name,' . $thematicArea->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $thematicArea->update($validated);

        return redirect()->route('admin.thematic-areas.index')
            ->with('success', 'Área temática actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThematicArea $thematicArea)
    {
        $thematicArea->delete();

        return redirect()->route('admin.thematic-areas.index')
            ->with('success', 'Área temática eliminada exitosamente.');
    }
}
