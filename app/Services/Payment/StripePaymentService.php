<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class StripePaymentService implements PaymentProviderInterface
{
    protected string $apiKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->apiKey = config('services.stripe.key');
        $this->secretKey = config('services.stripe.secret');
    }

    public function createPayment(Payment $payment): array
    {
        // Implementación con Stripe SDK
        // Por ahora, retornamos estructura básica
        return [
            'payment_intent_id' => 'pi_' . uniqid(),
            'client_secret' => 'secret_' . uniqid(),
            'checkout_url' => route('payment.stripe.checkout', $payment),
        ];
    }

    public function processPayment(Payment $payment): string
    {
        $result = $this->createPayment($payment);
        
        // Guardar el ID del proveedor
        $payment->update([
            'payment_provider_id' => $result['payment_intent_id'],
            'metadata' => $result,
        ]);

        return $result['checkout_url'];
    }

    public function verifyPayment(string $paymentId): array
    {
        // Verificar con Stripe API
        return [
            'status' => 'completed',
            'amount' => 0,
        ];
    }

    public function handleWebhook(array $payload): Payment
    {
        // Procesar webhook de Stripe
        $paymentIntentId = $payload['data']['object']['id'] ?? null;
        
        if (!$paymentIntentId) {
            throw new \Exception('Invalid webhook payload');
        }

        $payment = Payment::where('payment_provider_id', $paymentIntentId)->firstOrFail();

        // Actualizar estado según el evento
        $eventType = $payload['type'] ?? '';
        
        switch ($eventType) {
            case 'payment_intent.succeeded':
                $payment->markAsPaid();
                break;
            case 'payment_intent.payment_failed':
                $payment->markAsFailed($payload['data']['object']['last_payment_error']['message'] ?? 'Payment failed');
                break;
        }

        return $payment;
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        // Procesar reembolso con Stripe
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Aquí se haría la llamada real a Stripe API
            // Stripe::refunds()->create(['payment_intent' => $payment->payment_provider_id, 'amount' => $refundAmount * 100]);
            
            $payment->refund();
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe refund error: ' . $e->getMessage());
            return false;
        }
    }
}

