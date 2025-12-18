<?php

namespace App\Services;

use App\Models\Congress;
use App\Models\User;

class CertificateValidationService
{
    /**
     * Validate requirements for certificate generation
     */
    public function validateRequirements(Congress $congress, User $user, string $certificateType): array
    {
        $validations = [];
        $missingRequirements = [];

        // Requisitos comunes
        $validations['user_registered'] = $user->isRegisteredInCongress($congress->id);
        if (!$validations['user_registered']) {
            $missingRequirements[] = 'Usuario no registrado en el congreso';
        }

        // Requisitos según tipo de certificado
        switch ($certificateType) {
            case 'attendance':
                $validations['payment_complete'] = $this->checkPaymentComplete($congress, $user);
                if (!$validations['payment_complete']) {
                    $missingRequirements[] = 'Pago de inscripción incompleto';
                }
                break;

            case 'presentation':
                $validations['payment_complete'] = $this->checkPaymentComplete($congress, $user);
                $validations['paper_accepted'] = $this->checkPaperAccepted($congress, $user);
                
                if (!$validations['payment_complete']) {
                    $missingRequirements[] = 'Pago de inscripción incompleto';
                }
                if (!$validations['paper_accepted']) {
                    $missingRequirements[] = 'No tienes papers aceptados';
                }
                break;

            case 'participation':
                $validations['payment_complete'] = $this->checkPaymentComplete($congress, $user);
                $validations['has_participation'] = $this->checkHasParticipation($congress, $user);
                
                if (!$validations['payment_complete']) {
                    $missingRequirements[] = 'Pago de inscripción incompleto';
                }
                if (!$validations['has_participation']) {
                    $missingRequirements[] = 'No hay registro de participación';
                }
                break;
        }

        return [
            'is_valid' => empty($missingRequirements),
            'missing_requirements' => $missingRequirements,
            'validations' => $validations,
        ];
    }

    /**
     * Check if payment is complete
     */
    private function checkPaymentComplete(Congress $congress, User $user): bool
    {
        $registration = $user->registrations()
            ->where('congress_id', $congress->id)
            ->where('payment_status', 'completed')
            ->first();

        return $registration !== null;
    }

    /**
     * Check if user has accepted papers
     */
    private function checkPaperAccepted(Congress $congress, User $user): bool
    {
        return $congress->papers()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Check if user has participation (comments, questions, etc.)
     */
    private function checkHasParticipation(Congress $congress, User $user): bool
    {
        // Verificar si tiene comentarios o preguntas en sesiones
        $hasComments = \App\Models\SessionComment::whereHas('session', function($q) use ($congress) {
                $q->where('congress_id', $congress->id);
            })
            ->where('user_id', $user->id)
            ->exists();

        return $hasComments;
    }
}

