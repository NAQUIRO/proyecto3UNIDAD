<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class MercadoPagoPaymentService implements PaymentProviderInterface
{
    protected string $accessToken;
    protected bool $sandbox;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');
        $this->sandbox = config('services.mercadopago.sandbox', true);
    }

    public function createPayment(Payment $payment): array
    {
        // Implementación con MercadoPago SDK
        return [
            'preference_id' => 'MP_' . uniqid(),
            'init_point' => route('payment.mercadopago.checkout', $payment),
        ];
    }

    public function processPayment(Payment $payment): string
    {
        $result = $this->createPayment($payment);
        
        $payment->update([
            'payment_provider_id' => $result['preference_id'],
            'metadata' => $result,
        ]);

        return $result['init_point'];
    }

    public function verifyPayment(string $paymentId): array
    {
        // Verificar con MercadoPago API
        return [
            'status' => 'completed',
            'amount' => 0,
        ];
    }

    public function handleWebhook(array $payload): Payment
    {
        // Procesar webhook de MercadoPago
        $paymentId = $payload['data']['id'] ?? null;
        
        if (!$paymentId) {
            throw new \Exception('Invalid webhook payload');
        }

        $payment = Payment::where('payment_provider_id', $paymentId)->firstOrFail();

        $action = $payload['action'] ?? '';
        $status = $payload['data']['status'] ?? '';

        if ($status === 'approved') {
            $payment->markAsPaid();
        } elseif ($status === 'rejected') {
            $payment->markAsFailed('Payment rejected by MercadoPago');
        }

        return $payment;
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Aquí se haría la llamada real a MercadoPago API
            $payment->refund();
            return true;
        } catch (\Exception $e) {
            Log::error('MercadoPago refund error: ' . $e->getMessage());
            return false;
        }
    }
}

