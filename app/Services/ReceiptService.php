<?php

namespace App\Services;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    /**
     * Generar recibo PDF para un pago
     */
    public function generateReceipt(Payment $payment): string
    {
        $payment->load(['user', 'congress', 'registration']);

        $data = [
            'payment' => $payment,
            'user' => $payment->user,
            'congress' => $payment->congress,
            'registration' => $payment->registration,
            'receipt_number' => $this->generateReceiptNumber($payment),
            'date' => now()->format('d/m/Y H:i:s'),
        ];

        // Generar PDF
        $pdf = Pdf::loadView('receipts.payment', $data);
        
        // Guardar en storage
        $filename = 'receipts/' . $payment->id . '_' . time() . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        // Actualizar payment con la URL del recibo
        $payment->update([
            'receipt_url' => Storage::url($filename),
        ]);

        return Storage::url($filename);
    }

    /**
     * Generar número de recibo único
     */
    protected function generateReceiptNumber(Payment $payment): string
    {
        $prefix = 'REC';
        $congressCode = strtoupper(substr($payment->congress->slug, 0, 3));
        $year = now()->format('Y');
        $number = str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$congressCode}-{$year}-{$number}";
    }

    /**
     * Descargar recibo
     */
    public function downloadReceipt(Payment $payment): \Illuminate\Http\Response
    {
        if (!$payment->receipt_url) {
            $this->generateReceipt($payment);
        }

        $filePath = str_replace('/storage/', '', $payment->receipt_url);
        $fullPath = Storage::disk('public')->path($filePath);

        return response()->download($fullPath, "recibo_{$payment->id}.pdf");
    }
}

