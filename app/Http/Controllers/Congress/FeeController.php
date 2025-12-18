<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\CongressFee;
use Illuminate\Http\Request;

class FeeController extends Controller
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
        $fees = $congress->fees()->orderBy('sort_order')->orderBy('start_date')->get();
        return view('congress.fees.index', compact('congress', 'fees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        return view('congress.fees.create', compact('congress'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_type' => 'required|in:attendee,speaker,both',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['is_active'] = $request->has('is_active');

        CongressFee::create($validated);

        return redirect()->route('congress.fees.index', $congress)
            ->with('success', 'Tarifa creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, CongressFee $fee)
    {
        return view('congress.fees.edit', compact('congress', 'fee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, CongressFee $fee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_type' => 'required|in:attendee,speaker,both',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $fee->update($validated);

        return redirect()->route('congress.fees.index', $congress)
            ->with('success', 'Tarifa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, CongressFee $fee)
    {
        $fee->delete();

        return redirect()->route('congress.fees.index', $congress)
            ->with('success', 'Tarifa eliminada exitosamente.');
    }
}
