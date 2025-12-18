<?php

namespace App\Http\Controllers\Congress;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\CongressMilestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
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
        $milestones = $congress->milestones()->orderBy('deadline')->get();
        return view('congress.milestones.index', compact('congress', 'milestones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Congress $congress)
    {
        return view('congress.milestones.create', compact('congress'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Congress $congress)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'blocks_actions' => 'boolean',
            'type' => 'required|in:registration_start,registration_end,paper_submission_start,paper_submission_end,review_start,review_end,event_start,event_end,custom',
            'is_active' => 'boolean',
        ]);

        $validated['congress_id'] = $congress->id;
        $validated['blocks_actions'] = $request->has('blocks_actions');
        $validated['is_active'] = $request->has('is_active');

        CongressMilestone::create($validated);

        return redirect()->route('congress.milestones.index', $congress)
            ->with('success', 'Hito creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congress $congress, CongressMilestone $milestone)
    {
        return view('congress.milestones.edit', compact('congress', 'milestone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Congress $congress, CongressMilestone $milestone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'blocks_actions' => 'boolean',
            'type' => 'required|in:registration_start,registration_end,paper_submission_start,paper_submission_end,review_start,review_end,event_start,event_end,custom',
            'is_active' => 'boolean',
        ]);

        $validated['blocks_actions'] = $request->has('blocks_actions');
        $validated['is_active'] = $request->has('is_active');

        $milestone->update($validated);

        return redirect()->route('congress.milestones.index', $congress)
            ->with('success', 'Hito actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Congress $congress, CongressMilestone $milestone)
    {
        $milestone->delete();

        return redirect()->route('congress.milestones.index', $congress)
            ->with('success', 'Hito eliminado exitosamente.');
    }
}
