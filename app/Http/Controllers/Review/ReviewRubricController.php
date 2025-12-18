<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewRubric;
use Illuminate\Http\Request;

class ReviewRubricController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update a rubric score
     */
    public function update(Request $request, Review $review, ReviewRubric $rubric)
    {
        // Verificar que el rubric pertenezca al review y el review al revisor
        if ($rubric->review_id !== $review->id || $review->reviewer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $rubric->max_score,
            'comments' => 'nullable|string|max:1000',
        ]);

        $rubric->update($validated);

        // Recalcular puntuación general
        $overallScore = $review->calculateOverallScore();
        $review->update(['overall_score' => $overallScore]);

        return response()->json([
            'success' => true,
            'score' => $rubric->score,
            'overall_score' => $overallScore,
        ]);
    }
}
