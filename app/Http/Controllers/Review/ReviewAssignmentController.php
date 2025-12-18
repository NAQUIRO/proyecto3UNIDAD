<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Mail\ReviewAssignmentMail;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of assignments for a congress
     */
    public function index(Congress $congress)
    {
        // Solo admins del congreso pueden ver asignaciones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $assignments = ReviewAssignment::where('congress_id', $congress->id)
            ->with(['paper.author', 'reviewer', 'assignedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $papers = $congress->papers()
            ->where('status', 'submitted')
            ->orWhere('status', 'under_review')
            ->with('author', 'thematicArea')
            ->get();

        $reviewers = $congress->reviewers()->get();

        return view('review.assignments.index', compact('congress', 'assignments', 'papers', 'reviewers'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins del congreso pueden asignar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Verificar que el revisor sea revisor del congreso
        $reviewer = User::find($validated['reviewer_id']);
        if (!$reviewer->isReviewerForCongress($congress->id)) {
            return back()->with('error', 'El usuario seleccionado no es revisor de este congreso.');
        }

        // Verificar que no haya una asignación pendiente o aceptada
        $existingAssignment = ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', $validated['reviewer_id'])
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingAssignment) {
            return back()->with('error', 'Ya existe una asignación activa para este revisor y paper.');
        }

        $validated['congress_id'] = $congress->id;
        $validated['paper_id'] = $paper->id;
        $validated['assigned_by'] = auth()->id();
        $validated['assigned_at'] = now();

        $assignment = ReviewAssignment::create($validated);

        // Crear el review asociado
        $assignment->review()->create([
            'paper_id' => $paper->id,
            'reviewer_id' => $validated['reviewer_id'],
            'review_assignment_id' => $assignment->id,
            'status' => 'pending',
            'is_blind_review' => true,
            'assigned_at' => now(),
        ]);

        // Actualizar estado del paper
        if ($paper->status === 'submitted') {
            $paper->update(['status' => 'under_review', 'review_status' => 'in_progress']);
        }

        // Enviar notificación por email al revisor
        Mail::to($reviewer->email)->send(new ReviewAssignmentMail($assignment));

        return back()->with('success', 'Revisor asignado exitosamente.');
    }

    /**
     * Accept assignment (for reviewers)
     */
    public function accept(Congress $congress, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== auth()->id()) {
            abort(403);
        }

        $assignment->accept();

        return back()->with('success', 'Asignación aceptada. Puedes comenzar la revisión.');
    }

    /**
     * Reject assignment (for reviewers)
     */
    public function reject(Congress $congress, ReviewAssignment $assignment)
    {
        if ($assignment->reviewer_id !== auth()->id()) {
            abort(403);
        }

        $assignment->reject();

        return back()->with('success', 'Asignación rechazada.');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Congress $congress, ReviewAssignment $assignment)
    {
        // Solo admins pueden eliminar asignaciones
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        // Eliminar el review asociado si existe
        if ($assignment->review) {
            $assignment->review->delete();
        }

        $assignment->delete();

        return back()->with('success', 'Asignación eliminada exitosamente.');
    }
}
