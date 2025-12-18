<?php

namespace App\Services;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateGeneratorService
{
    /**
     * Generate certificate PDF
     */
    public function generate(Certificate $certificate): string
    {
        $certificate->load(['user', 'congress']);

        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'congress' => $certificate->congress,
            'date' => now()->format('d/m/Y'),
        ];

        // Generar PDF
        $pdf = Pdf::loadView('certificates.template', $data);
        
        // Guardar archivo
        $fileName = "certificates/{$certificate->congress_id}/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}

