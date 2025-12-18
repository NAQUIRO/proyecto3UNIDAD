<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Mail\ReviewCompletedMail;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\Review;
use App\Models\ReviewRubric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of reviews for a reviewer
     */
    public function index(Congress $congress)
    {
        $reviews = Review::whereHas('paper', function($query) use ($congress) {
                $query->where('congress_id', $congress->id);
            })
            ->where('reviewer_id', auth()->id())
            ->with(['paper.author', 'paper.thematicArea'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('review.index', compact('congress', 'reviews'));
    }

    /**
     * Display the specified review
     */
    public function show(Congress $congress, Paper $paper, Review $review)
    {
        // Verificar que el review pertenezca al paper y al revisor
        if ($review->paper_id !== $paper->id || $review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        // Si es la primera vez que accede, marcar como iniciado
        if ($review->status === 'pending') {
            $review->start();
        }

        $review->load(['paper.author', 'paper.thematicArea', 'paper.coauthors', 'rubrics']);

        return view('review.show', compact('congress', 'paper', 'review'));
    }

    /**
     * Show the form for editing the specified review
     */
    public function edit(Congress $congress, Paper $paper, Review $review)
    {
        // Verificar que el review pertenezca al paper y al revisor
        if ($review->paper_id !== $paper->id || $review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        // No permitir editar si ya está completado
        if ($review->isCompleted()) {
            return redirect()->route('review.show', [$congress, $paper, $review])
                ->with('error', 'No puedes editar una revisión completada.');
        }

        $review->load(['rubrics']);

        // Si no hay rúbricas, crear las predeterminadas
        if ($review->rubrics->isEmpty()) {
            $this->createDefaultRubrics($review);
            $review->refresh();
        }

        return view('review.edit', compact('congress', 'paper', 'review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Congress $congress, Paper $paper, Review $review)
    {
        // Verificar que el review pertenezca al paper y al revisor
        if ($review->paper_id !== $paper->id || $review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        // No permitir editar si ya está completado
        if ($review->isCompleted()) {
            return redirect()->route('review.show', [$congress, $paper, $review])
                ->with('error', 'No puedes editar una revisión completada.');
        }

        $validated = $request->validate([
            'recommendation' => 'required|in:accept,reject,revision_required',
            'comments' => 'nullable|string',
            'confidential_comments' => 'nullable|string',
            'rubrics' => 'required|array',
            'rubrics.*.id' => 'required|exists:review_rubrics,id',
            'rubrics.*.score' => 'required|numeric|min:0',
            'rubrics.*.comments' => 'nullable|string',
        ]);

        // Actualizar rúbricas
        foreach ($validated['rubrics'] as $rubricData) {
            $rubric = ReviewRubric::find($rubricData['id']);
            if ($rubric && $rubric->review_id === $review->id) {
                $rubric->update([
                    'score' => $rubricData['score'],
                    'comments' => $rubricData['comments'] ?? null,
                ]);
            }
        }

        // Calcular puntuación general
        $overallScore = $review->calculateOverallScore();

        // Completar la revisión
        $review->complete($validated['recommendation'], [
            'comments' => $validated['comments'] ?? null,
            'confidential_comments' => $validated['confidential_comments'] ?? null,
            'overall_score' => $overallScore,
        ]);

        // Enviar notificación por email al autor
        Mail::to($paper->author->email)->send(new ReviewCompletedMail($review));

        return redirect()->route('review.show', [$congress, $paper, $review])
            ->with('success', 'Revisión completada exitosamente.');
    }

    /**
     * Create default rubrics for a review
     */
    private function createDefaultRubrics(Review $review): void
    {
        $defaultRubrics = [
            ['criterion' => 'Originalidad', 'description' => 'Novedad y contribución del trabajo', 'max_score' => 10, 'order' => 1],
            ['criterion' => 'Metodología', 'description' => 'Rigor metodológico y adecuación de los métodos', 'max_score' => 10, 'order' => 2],
            ['criterion' => 'Resultados', 'description' => 'Claridad y relevancia de los resultados', 'max_score' => 10, 'order' => 3],
            ['criterion' => 'Redacción', 'description' => 'Claridad, coherencia y calidad de la escritura', 'max_score' => 10, 'order' => 4],
            ['criterion' => 'Relevancia', 'description' => 'Relevancia para el área temática y el congreso', 'max_score' => 10, 'order' => 5],
        ];

        foreach ($defaultRubrics as $rubricData) {
            ReviewRubric::create(array_merge($rubricData, [
                'review_id' => $review->id,
                'score' => 0,
            ]));
        }
    }
}
