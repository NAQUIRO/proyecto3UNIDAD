<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;

class ManualPaymentService implements PaymentProviderInterface
{
    public function createPayment(Payment $payment): array
    {
        return [
            'status' => 'pending_approval',
            'message' => 'Pago pendiente de aprobación manual por el administrador',
        ];
    }

    public function processPayment(Payment $payment): string
    {
        // Para pagos manuales, no hay URL de checkout
        $payment->update([
            'status' => 'pending',
            'metadata' => $this->createPayment($payment),
        ]);

        return route('payment.show', [$payment->congress, $payment]);
    }

    public function verifyPayment(string $paymentId): array
    {
        $payment = Payment::findOrFail($paymentId);
        
        return [
            'status' => $payment->status,
            'amount' => $payment->amount,
        ];
    }

    public function handleWebhook(array $payload): Payment
    {
        // Los pagos manuales no tienen webhooks
        throw new \Exception('Manual payments do not support webhooks');
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        // Para pagos manuales, el reembolso se hace manualmente
        $payment->refund();
        return true;
    }
}

