<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\ThematicArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CongressController extends Controller
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
        $congresses = Congress::with('thematicAreas', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.congresses.index', compact('congresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $thematicAreas = ThematicArea::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('admin.congresses.create', compact('thematicAreas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'url' => 'nullable|string|max:255|unique:congresses,url',
            'status' => 'required|in:draft,published,finished',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'thematic_areas' => 'nullable|array',
            'thematic_areas.*' => 'exists:thematic_areas,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['created_by'] = auth()->id();

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('congresses/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('congresses/banners', 'public');
        }

        $thematicAreas = $validated['thematic_areas'] ?? [];
        unset($validated['thematic_areas']);

        $congress = Congress::create($validated);
        $congress->thematicAreas()->sync($thematicAreas);

        return redirect()->route('admin.congresses.index')
            ->with('success', 'Congreso creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Congress $congress)
    {
        $congress->load('thematicAreas', 'creator');
        return view('admin.congresses.show', compact('congress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress)
    {
        $thematicAreas = ThematicArea::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        $congress->load('thematicAreas');
        
        return view('admin.congresses.edit', compact('congress', 'thematicAreas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'url' => 'nullable|string|max:255|unique:congresses,url,' . $congress->id,
            'status' => 'required|in:draft,published,finished',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'thematic_areas' => 'nullable|array',
            'thematic_areas.*' => 'exists:thematic_areas,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            if ($congress->logo) {
                Storage::disk('public')->delete($congress->logo);
            }
            $validated['logo'] = $request->file('logo')->store('congresses/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($congress->banner) {
                Storage::disk('public')->delete($congress->banner);
            }
            $validated['banner'] = $request->file('banner')->store('congresses/banners', 'public');
        }

        $thematicAreas = $validated['thematic_areas'] ?? [];
        unset($validated['thematic_areas']);

        $congress->update($validated);
        $congress->thematicAreas()->sync($thematicAreas);

        return redirect()->route('admin.congresses.index')
            ->with('success', 'Congreso actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress)
    {
        if ($congress->logo) {
            Storage::disk('public')->delete($congress->logo);
        }
        if ($congress->banner) {
            Storage::disk('public')->delete($congress->banner);
        }

        $congress->delete();

        return redirect()->route('admin.congresses.index')
            ->with('success', 'Congreso eliminado exitosamente.');
    }
}
