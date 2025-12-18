<?php

namespace App\Services;

use App\Models\Congress;
use App\Models\Paper;
use App\Models\ReviewAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewerAssignmentService
{
    /**
     * Asignar revisores automáticamente basado en área temática
     */
    public function assignByThematicArea(Paper $paper, int $numberOfReviewers = 2): array
    {
        $assignments = [];
        
        // Obtener revisores que tienen experiencia en el área temática del paper
        $reviewers = $this->getReviewersForThematicArea($paper->congress, $paper->thematic_area_id);
        
        // Filtrar revisores que ya tienen asignaciones pendientes o activas para este paper
        $reviewers = $reviewers->filter(function ($reviewer) use ($paper) {
            return !$this->hasActiveAssignment($paper, $reviewer->id);
        });

        // Seleccionar aleatoriamente el número solicitado de revisores
        $selectedReviewers = $reviewers->shuffle()->take($numberOfReviewers);

        foreach ($selectedReviewers as $reviewer) {
            $assignment = $this->createAssignment($paper, $reviewer->id);
            if ($assignment) {
                $assignments[] = $assignment;
            }
        }

        return $assignments;
    }

    /**
     * Asignar revisores manualmente
     */
    public function assignManually(Paper $paper, array $reviewerIds, ?\DateTime $deadline = null, ?string $notes = null): array
    {
        $assignments = [];

        foreach ($reviewerIds as $reviewerId) {
            // Verificar que no haya una asignación activa
            if ($this->hasActiveAssignment($paper, $reviewerId)) {
                continue;
            }

            $assignment = $this->createAssignment($paper, $reviewerId, $deadline, $notes);
            if ($assignment) {
                $assignments[] = $assignment;
            }
        }

        return $assignments;
    }

    /**
     * Obtener revisores disponibles para un área temática
     */
    protected function getReviewersForThematicArea(Congress $congress, int $thematicAreaId)
    {
        // Obtener usuarios que son revisores del congreso y tienen experiencia en el área temática
        return User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Revisor', 'Super Admin', 'Admin']);
        })
        ->whereHas('congresses', function ($query) use ($congress) {
            $query->where('congress_id', $congress->id)
                  ->where('role', 'reviewer');
        })
        ->get();
    }

    /**
     * Verificar si un revisor ya tiene una asignación activa para un paper
     */
    protected function hasActiveAssignment(Paper $paper, int $reviewerId): bool
    {
        return ReviewAssignment::where('paper_id', $paper->id)
            ->where('reviewer_id', $reviewerId)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
    }

    /**
     * Crear una asignación de revisión
     */
    protected function createAssignment(Paper $paper, int $reviewerId, ?\DateTime $deadline = null, ?string $notes = null): ?ReviewAssignment
    {
        try {
            DB::beginTransaction();

            $assignment = ReviewAssignment::create([
                'congress_id' => $paper->congress_id,
                'paper_id' => $paper->id,
                'reviewer_id' => $reviewerId,
                'assigned_by' => auth()->id(),
                'status' => 'pending',
                'deadline' => $deadline,
                'notes' => $notes,
                'assigned_at' => now(),
            ]);

            // Crear el review asociado (doble ciego)
            $assignment->review()->create([
                'paper_id' => $paper->id,
                'reviewer_id' => $reviewerId,
                'review_assignment_id' => $assignment->id,
                'status' => 'pending',
                'is_blind_review' => true,
                'assigned_at' => now(),
            ]);

            // Actualizar estado del paper si es necesario
            if ($paper->status === 'submitted') {
                $paper->update([
                    'status' => 'under_review',
                    'review_status' => 'in_progress',
                ]);
            }

            DB::commit();

            // Despachar job de notificación
            \App\Jobs\SendPaperNotificationJob::dispatch($assignment);

            return $assignment;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating review assignment: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sugerir revisores basado en área temática y carga de trabajo
     */
    public function suggestReviewers(Paper $paper, int $limit = 5): \Illuminate\Support\Collection
    {
        $reviewers = $this->getReviewersForThematicArea($paper->congress, $paper->thematic_area_id);

        // Filtrar revisores con asignaciones activas para este paper
        $reviewers = $reviewers->filter(function ($reviewer) use ($paper) {
            return !$this->hasActiveAssignment($paper, $reviewer->id);
        });

        // Ordenar por carga de trabajo (menos asignaciones pendientes primero)
        $reviewers = $reviewers->map(function ($reviewer) use ($paper) {
            $pendingCount = ReviewAssignment::where('reviewer_id', $reviewer->id)
                ->where('congress_id', $paper->congress_id)
                ->whereIn('status', ['pending', 'accepted'])
                ->count();
            
            $reviewer->pending_assignments = $pendingCount;
            return $reviewer;
        })->sortBy('pending_assignments');

        return $reviewers->take($limit);
    }
}

