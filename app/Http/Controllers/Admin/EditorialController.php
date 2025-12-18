<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Editorial;
use Illuminate\Http\Request;

class EditorialController extends Controller
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
        $editorials = Editorial::orderBy('sort_order')->orderBy('name')->paginate(15);
        return view('admin.editorials.index', compact('editorials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.editorials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:editorials,name',
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Editorial::create($validated);

        return redirect()->route('admin.editorials.index')
            ->with('success', 'Editorial creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Editorial $editorial)
    {
        return view('admin.editorials.edit', compact('editorial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Editorial $editorial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:editorials,name,' . $editorial->id,
            'description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $editorial->update($validated);

        return redirect()->route('admin.editorials.index')
            ->with('success', 'Editorial actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Editorial $editorial)
    {
        $editorial->delete();

        return redirect()->route('admin.editorials.index')
            ->with('success', 'Editorial eliminada exitosamente.');
    }
}
