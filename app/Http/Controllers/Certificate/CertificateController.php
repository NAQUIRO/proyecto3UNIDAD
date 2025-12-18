<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Congress;
use App\Models\Paper;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Validate and generate certificate for a user
     */
    public function validateAndGenerate(Congress $congress)
    {
        $user = auth()->user();

        // Validar requisitos
        $validation = $this->validateRequirements($congress, $user);

        if (!$validation['is_valid']) {
            return back()->with('error', 'No cumples con los requisitos para obtener el certificado: ' . implode(', ', $validation['failed_requirements']));
        }

        // Verificar si ya tiene certificado
        $existingCertificate = Certificate::where('congress_id', $congress->id)
            ->where('user_id', $user->id)
            ->where('type', $validation['certificate_type'])
            ->where('is_valid', true)
            ->first();

        if ($existingCertificate && $existingCertificate->isGenerated()) {
            return redirect()->route('certificate.show', [$congress, $existingCertificate])
                ->with('info', 'Ya tienes un certificado generado.');
        }

        // Crear o actualizar certificado
        $certificate = $existingCertificate ?? Certificate::create([
            'congress_id' => $congress->id,
            'user_id' => $user->id,
            'type' => $validation['certificate_type'],
            'requirements_met' => $validation['met_requirements'],
            'requirements_failed' => [],
            'is_valid' => true,
            'status' => 'pending',
        ]);

        // Generar PDF
        $this->generateCertificatePdf($certificate);

        return redirect()->route('certificate.show', [$congress, $certificate])
            ->with('success', 'Certificado generado exitosamente.');
    }

    /**
     * Display certificate
     */
    public function show(Congress $congress, Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $certificate->load(['user', 'template', 'congress']);

        return view('certificate.show', compact('congress', 'certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download(Congress $congress, Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            // Regenerar si no existe
            $this->generateCertificatePdf($certificate);
        }

        return Storage::disk('public')->download($certificate->pdf_path, "certificado_{$certificate->certificate_number}.pdf");
    }

    /**
     * Validate requirements for certificate
     */
    private function validateRequirements(Congress $congress, $user): array
    {
        $metRequirements = [];
        $failedRequirements = [];
        $certificateType = 'attendance';

        // Requisito 1: Inscripción confirmada y pagada
        $registration = Registration::where('congress_id', $congress->id)
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('payment_status', 'completed')
            ->first();

        if ($registration) {
            $metRequirements[] = 'Inscripción confirmada y pagada';
        } else {
            $failedRequirements[] = 'Inscripción no confirmada o no pagada';
        }

        // Requisito 2: Paper aceptado (para certificado de presentación)
        $acceptedPaper = Paper::where('congress_id', $congress->id)
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if ($acceptedPaper) {
            $metRequirements[] = 'Paper aceptado';
            $certificateType = 'presentation';
        }

        // Requisito 3: Asistencia (podría verificarse con logs de sesiones virtuales)
        // Por ahora, asumimos que si está registrado y pagado, asistió

        $isValid = count($failedRequirements) === 0 && count($metRequirements) > 0;

        return [
            'is_valid' => $isValid,
            'met_requirements' => $metRequirements,
            'failed_requirements' => $failedRequirements,
            'certificate_type' => $certificateType,
        ];
    }

    /**
     * Generate certificate PDF
     */
    private function generateCertificatePdf(Certificate $certificate): void
    {
        $template = $certificate->template ?? CertificateTemplate::where('congress_id', $certificate->congress_id)
            ->where('type', $certificate->type)
            ->where('is_default', true)
            ->first();

        if (!$template) {
            // Usar plantilla por defecto
            $html = $this->getDefaultTemplate($certificate);
        } else {
            $html = $this->renderTemplate($template, $certificate);
        }

        // Generar PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');

        $filename = 'certificates/' . $certificate->congress_id . '/' . $certificate->certificate_number . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $certificate->update([
            'pdf_path' => $filename,
            'status' => 'generated',
            'generated_at' => now(),
        ]);
    }

    /**
     * Render template with data
     */
    private function renderTemplate(CertificateTemplate $template, Certificate $certificate): string
    {
        $html = $template->html_template;
        $user = $certificate->user;
        $congress = $certificate->congress;

        // Reemplazar variables
        $replacements = [
            '{{USER_NAME}}' => $user->name,
            '{{CONGRESS_NAME}}' => $congress->title,
            '{{CERTIFICATE_NUMBER}}' => $certificate->certificate_number,
            '{{DATE}}' => now()->format('d/m/Y'),
            '{{TYPE}}' => $this->getCertificateTypeName($certificate->type),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    /**
     * Get default template
     */
    private function getDefaultTemplate(Certificate $certificate): string
    {
        $user = $certificate->user;
        $congress = $certificate->congress;
        $typeName = $this->getCertificateTypeName($certificate->type);

        return view('certificates.default', compact('certificate', 'user', 'congress', 'typeName'))->render();
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
}
