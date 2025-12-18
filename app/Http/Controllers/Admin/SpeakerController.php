<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpeakerController extends Controller
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
        $speakers = Speaker::with('congress')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.speakers.index', compact('speakers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('admin.speakers.create', compact('congresses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'congress_id' => 'nullable|exists:congresses,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'institution' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'specialization' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('speakers/photos', 'public');
        }

        Speaker::create($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Ponente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Speaker $speaker)
    {
        $speaker->load('congress', 'papers');
        return view('admin.speakers.show', compact('speaker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Speaker $speaker)
    {
        $congresses = Congress::where('status', 'published')
            ->orderBy('title')
            ->get();

        return view('admin.speakers.edit', compact('speaker', 'congresses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speaker $speaker)
    {
        $validated = $request->validate([
            'congress_id' => 'nullable|exists:congresses,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'institution' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'specialization' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($speaker->photo) {
                Storage::disk('public')->delete($speaker->photo);
            }
            $validated['photo'] = $request->file('photo')->store('speakers/photos', 'public');
        }

        $speaker->update($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Ponente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speaker $speaker)
    {
        if ($speaker->photo) {
            Storage::disk('public')->delete($speaker->photo);
        }

        $speaker->delete();

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Ponente eliminado exitosamente.');
    }
}
