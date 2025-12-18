<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Mail\ReviewAssignmentMail;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\ReviewAssignment;
use App\Models\User;
use App\Services\ReviewerAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewAssignmentController extends Controller
{
    protected ReviewerAssignmentService $assignmentService;

    public function __construct(ReviewerAssignmentService $assignmentService)
    {
        $this->middleware('auth');
        $this->assignmentService = $assignmentService;
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
            ->whereIn('status', ['submitted', 'under_review'])
            ->with('author', 'thematicArea')
            ->get();

        $reviewers = $congress->reviewers()->get();

        return view('review.assignments.index', compact('congress', 'assignments', 'papers', 'reviewers'));
    }

    /**
     * Store a newly created assignment (manual)
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

        $deadline = $validated['deadline'] ? new \DateTime($validated['deadline']) : null;

        $assignment = $this->assignmentService->assignManually(
            $paper,
            [$validated['reviewer_id']],
            $deadline,
            $validated['notes'] ?? null
        );

        if (empty($assignment)) {
            return back()->with('error', 'No se pudo crear la asignación. Verifica que el revisor sea válido y no tenga una asignación activa.');
        }

        return back()->with('success', 'Revisor asignado exitosamente.');
    }

    /**
     * Asignar revisores automáticamente por área temática
     */
    public function assignAuto(Request $request, Congress $congress, Paper $paper)
    {
        // Solo admins del congreso pueden asignar
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $validated = $request->validate([
            'number_of_reviewers' => 'nullable|integer|min:1|max:5',
        ]);

        $numberOfReviewers = $validated['number_of_reviewers'] ?? 2;

        $assignments = $this->assignmentService->assignByThematicArea($paper, $numberOfReviewers);

        if (empty($assignments)) {
            return back()->with('error', 'No se encontraron revisores disponibles para el área temática de este paper.');
        }

        return back()->with('success', count($assignments) . ' revisor(es) asignado(s) automáticamente.');
    }

    /**
     * Obtener sugerencias de revisores
     */
    public function suggestReviewers(Congress $congress, Paper $paper)
    {
        // Solo admins del congreso pueden ver sugerencias
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $suggestions = $this->assignmentService->suggestReviewers($paper, 10);

        return response()->json($suggestions->values());
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
