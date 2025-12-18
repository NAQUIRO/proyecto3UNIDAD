<?php

namespace App\Services;

use App\Jobs\SendBulkEmailJob;
use App\Models\Congress;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BulkEmailService
{
    /**
     * Enviar campaña usando chunks para evitar saturar memoria
     */
    public function sendCampaign(EmailCampaign $campaign, int $chunkSize = 50): void
    {
        $campaign->update(['status' => 'sending']);

        // Procesar destinatarios en chunks
        $campaign->recipients()
            ->where('status', 'pending')
            ->chunk($chunkSize, function ($recipients) use ($campaign) {
                foreach ($recipients as $recipient) {
                    // Despachar job para cada destinatario
                    SendBulkEmailJob::dispatch($recipient, $campaign->subject, $campaign->content);
                }
            });
    }

    /**
     * Preparar destinatarios para una campaña
     */
    public function prepareRecipients(Congress $congress, string $segmentType, ?array $filters = null): \Illuminate\Support\Collection
    {
        $recipients = match ($segmentType) {
            'all' => $this->getAllRecipients($congress),
            'attendees' => $this->getAttendees($congress),
            'speakers' => $this->getSpeakers($congress),
            'accepted_speakers' => $this->getAcceptedSpeakers($congress),
            'reviewers' => $this->getReviewers($congress),
            'custom' => $this->getCustomRecipients($congress, $filters ?? []),
            default => collect(),
        };

        return $recipients;
    }

    /**
     * Obtener todos los usuarios registrados en el congreso
     */
    protected function getAllRecipients(Congress $congress): \Illuminate\Support\Collection
    {
        return $congress->users()
            ->wherePivot('registration_status', 'confirmed')
            ->get();
    }

    /**
     * Obtener asistentes
     */
    protected function getAttendees(Congress $congress): \Illuminate\Support\Collection
    {
        return $congress->users()
            ->wherePivot('role', 'attendee')
            ->wherePivot('registration_status', 'confirmed')
            ->get();
    }

    /**
     * Obtener ponentes
     */
    protected function getSpeakers(Congress $congress): \Illuminate\Support\Collection
    {
        return $congress->users()
            ->wherePivot('role', 'speaker')
            ->wherePivot('registration_status', 'confirmed')
            ->get();
    }

    /**
     * Obtener ponentes con papers aceptados
     */
    protected function getAcceptedSpeakers(Congress $congress): \Illuminate\Support\Collection
    {
        return User::whereHas('papers', function ($query) use ($congress) {
            $query->where('congress_id', $congress->id)
                  ->where('status', 'accepted');
        })->get();
    }

    /**
     * Obtener revisores
     */
    protected function getReviewers(Congress $congress): \Illuminate\Support\Collection
    {
        return $congress->reviewers()->get();
    }

    /**
     * Obtener destinatarios personalizados según filtros
     */
    protected function getCustomRecipients(Congress $congress, array $filters): \Illuminate\Support\Collection
    {
        $query = $congress->users();

        if (isset($filters['role'])) {
            $query->wherePivot('role', $filters['role']);
        }

        if (isset($filters['registration_status'])) {
            $query->wherePivot('registration_status', $filters['registration_status']);
        }

        if (isset($filters['thematic_area_id'])) {
            $query->whereHas('papers', function ($q) use ($filters) {
                $q->where('thematic_area_id', $filters['thematic_area_id']);
            });
        }

        return $query->get();
    }

    /**
     * Actualizar estadísticas de la campaña
     */
    public function updateCampaignStats(EmailCampaign $campaign): void
    {
        $sentCount = $campaign->recipients()->where('status', 'sent')->count();
        $failedCount = $campaign->recipients()->where('status', 'failed')->count();
        $pendingCount = $campaign->recipients()->where('status', 'pending')->count();

        $campaign->update([
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ]);

        // Si no hay más pendientes, marcar como enviada
        if ($pendingCount === 0) {
            $campaign->markAsSent();
        }
    }
}

