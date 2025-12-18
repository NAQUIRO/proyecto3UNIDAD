<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateGeneratorService
{
    /**
     * Generate certificate PDF
     */
    public function generate(Certificate $certificate): string
    {
        $certificate->load(['user', 'congress', 'template']);

        // Obtener plantilla
        $template = $certificate->template ?? CertificateTemplate::where('congress_id', $certificate->congress_id)
            ->where('type', $certificate->type)
            ->where('is_default', true)
            ->first();

        // Preparar datos
        $data = [
            'certificate' => $certificate,
            'user' => $certificate->user,
            'congress' => $certificate->congress,
            'typeName' => $this->getCertificateTypeName($certificate->type),
            'date' => now()->format('d/m/Y'),
        ];

        // Generar PDF
        if ($template && $template->html_template) {
            $html = $this->renderTemplate($template, $data);
            $pdf = Pdf::loadHTML($html);
        } else {
            $pdf = Pdf::loadView('certificates.default', $data);
        }

        // Configurar papel (horizontal para certificados)
        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        // Guardar archivo
        $fileName = "certificates/{$certificate->congress_id}/{$certificate->certificate_number}.pdf";
        Storage::disk('public')->put($fileName, $pdf->output());

        // Actualizar certificado
        $certificate->update([
            'pdf_path' => $fileName,
            'status' => 'generated',
            'generated_at' => now(),
        ]);

        return $fileName;
    }

    /**
     * Render template with data
     */
    private function renderTemplate(CertificateTemplate $template, array $data): string
    {
        $html = $template->html_template;
        $certificate = $data['certificate'];
        $user = $data['user'];
        $congress = $data['congress'];

        // Reemplazar variables
        $replacements = [
            '{{USER_NAME}}' => $user->name,
            '{{USER_EMAIL}}' => $user->email,
            '{{CONGRESS_NAME}}' => $congress->title,
            '{{CONGRESS_DATE}}' => $congress->start_date ? $congress->start_date->format('d/m/Y') : '',
            '{{CERTIFICATE_NUMBER}}' => $certificate->certificate_number,
            '{{DATE}}' => $data['date'],
            '{{TYPE}}' => $data['typeName'],
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Get certificate type name
     */
    private function getCertificateTypeName(string $type): string
    {
        return match ($type) {
            'attendance' => 'Asistencia',
            'presentation' => 'Presentación',
            'participation' => 'Participación',
            default => 'Participación',
        };
    }

    /**
     * Batch generate certificates
     */
    public function batchGenerate(array $certificateIds): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($certificateIds as $certificateId) {
            try {
                $certificate = Certificate::findOrFail($certificateId);
                $this->generate($certificate);
                $results['success'][] = $certificateId;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $certificateId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
