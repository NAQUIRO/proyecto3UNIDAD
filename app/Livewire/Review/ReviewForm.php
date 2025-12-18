<?php

namespace App\Livewire\Review;

use App\Models\Paper;
use App\Models\Review;
use App\Models\ReviewRubric;
use Livewire\Component;

class ReviewForm extends Component
{
    public Review $review;
    public Paper $paper;

    // Form fields
    public $recommendation = '';
    public $comments = '';
    public $confidential_comments = '';
    public $rubrics = [];

    // Default rubric criteria
    protected $defaultCriteria = [
        ['criterion' => 'Originalidad', 'description' => 'Novedad y contribución al campo', 'max_score' => 10],
        ['criterion' => 'Metodología', 'description' => 'Rigor metodológico y diseño de investigación', 'max_score' => 10],
        ['criterion' => 'Resultados', 'description' => 'Claridad y relevancia de los resultados', 'max_score' => 10],
        ['criterion' => 'Escritura', 'description' => 'Claridad, coherencia y calidad de la escritura', 'max_score' => 10],
        ['criterion' => 'Relevancia', 'description' => 'Relevancia para el congreso y el área temática', 'max_score' => 10],
    ];

    protected $rules = [
        'recommendation' => 'required|in:accept,reject,revision_required',
        'comments' => 'required|string|min:50',
        'confidential_comments' => 'nullable|string',
        'rubrics.*.score' => 'required|numeric|min:0',
        'rubrics.*.comments' => 'nullable|string',
    ];

    public function mount(Review $review)
    {
        $this->review = $review->load('paper', 'rubrics');
        $this->paper = $review->paper;

        // Verificar que el usuario autenticado sea el revisor
        if ($this->review->reviewer_id !== auth()->id()) {
            abort(403, 'No tienes permiso para revisar este paper.');
        }

        // Verificar que la revisión esté en estado pendiente o en progreso
        if (!in_array($this->review->status, ['pending', 'in_progress'])) {
            abort(403, 'Esta revisión ya ha sido completada.');
        }

        // Iniciar la revisión si está pendiente
        if ($this->review->status === 'pending') {
            $this->review->start();
        }

        // Cargar rubricas existentes o crear las predeterminadas
        if ($this->review->rubrics->isEmpty()) {
            $this->initializeRubrics();
        } else {
            $this->loadRubrics();
        }
    }

    protected function initializeRubrics()
    {
        foreach ($this->defaultCriteria as $index => $criterion) {
            $this->rubrics[] = [
                'criterion' => $criterion['criterion'],
                'description' => $criterion['description'],
                'max_score' => $criterion['max_score'],
                'score' => 0,
                'comments' => '',
            ];
        }
    }

    protected function loadRubrics()
    {
        $this->rubrics = $this->review->rubrics->map(function ($rubric) {
            return [
                'id' => $rubric->id,
                'criterion' => $rubric->criterion,
                'description' => $rubric->description,
                'max_score' => $rubric->max_score,
                'score' => $rubric->score,
                'comments' => $rubric->comments,
            ];
        })->toArray();
    }

    public function updatedRubrics($value, $key)
    {
        // Validar que el score no exceda el max_score
        if (str_contains($key, '.score')) {
            $index = (int) explode('.', $key)[0];
            if (isset($this->rubrics[$index])) {
                $maxScore = $this->rubrics[$index]['max_score'];
                if ($value > $maxScore) {
                    $this->rubrics[$index]['score'] = $maxScore;
                    $this->addError("rubrics.{$index}.score", "La puntuación no puede exceder {$maxScore}.");
                }
            }
        }
    }

    public function save()
    {
        $this->validate();

        // Calcular puntuación general
        $totalScore = 0;
        $maxTotalScore = 0;
        foreach ($this->rubrics as $rubric) {
            $totalScore += $rubric['score'];
            $maxTotalScore += $rubric['max_score'];
        }
        $overallScore = $maxTotalScore > 0 ? ($totalScore / $maxTotalScore) * 10 : 0;

        // Guardar o actualizar rubricas
        foreach ($this->rubrics as $index => $rubricData) {
            if (isset($rubricData['id'])) {
                // Actualizar rubrica existente
                ReviewRubric::where('id', $rubricData['id'])->update([
                    'score' => $rubricData['score'],
                    'comments' => $rubricData['comments'] ?? null,
                ]);
            } else {
                // Crear nueva rubrica
                ReviewRubric::create([
                    'review_id' => $this->review->id,
                    'criterion' => $rubricData['criterion'],
                    'description' => $rubricData['description'],
                    'score' => $rubricData['score'],
                    'max_score' => $rubricData['max_score'],
                    'comments' => $rubricData['comments'] ?? null,
                    'order' => $index,
                ]);
            }
        }

        // Completar la revisión
        $this->review->complete($this->recommendation, [
            'comments' => $this->comments,
            'confidential_comments' => $this->confidential_comments,
            'overall_score' => round($overallScore, 2),
        ]);

        // Actualizar la asignación
        if ($this->review->assignment) {
            $this->review->assignment->update(['status' => 'completed']);
        }

        // Despachar job de notificación
        \App\Jobs\SendReviewCompletedNotificationJob::dispatch($this->review);

        session()->flash('success', 'Revisión completada exitosamente.');

        return redirect()->route('review.show', [$this->paper->congress, $this->paper, $this->review]);
    }

    public function render()
    {
        return view('livewire.review.review-form');
    }
}
