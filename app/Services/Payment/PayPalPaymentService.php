<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PayPalPaymentService implements PaymentProviderInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected bool $sandbox;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.secret');
        $this->sandbox = config('services.paypal.sandbox', true);
    }

    public function createPayment(Payment $payment): array
    {
        // Implementación con PayPal SDK
        return [
            'order_id' => 'PAYPAL_' . uniqid(),
            'approval_url' => route('payment.paypal.approve', $payment),
        ];
    }

    public function processPayment(Payment $payment): string
    {
        $result = $this->createPayment($payment);
        
        $payment->update([
            'payment_provider_id' => $result['order_id'],
            'metadata' => $result,
        ]);

        return $result['approval_url'];
    }

    public function verifyPayment(string $paymentId): array
    {
        // Verificar con PayPal API
        return [
            'status' => 'completed',
            'amount' => 0,
        ];
    }

    public function handleWebhook(array $payload): Payment
    {
        // Procesar webhook de PayPal
        $orderId = $payload['resource']['id'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('Invalid webhook payload');
        }

        $payment = Payment::where('payment_provider_id', $orderId)->firstOrFail();

        $eventType = $payload['event_type'] ?? '';
        
        switch ($eventType) {
            case 'PAYMENT.CAPTURE.COMPLETED':
                $payment->markAsPaid();
                break;
            case 'PAYMENT.CAPTURE.DENIED':
                $payment->markAsFailed('Payment denied by PayPal');
                break;
        }

        return $payment;
    }

    public function refund(Payment $payment, ?float $amount = null): bool
    {
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Aquí se haría la llamada real a PayPal API
            $payment->refund();
            return true;
        } catch (\Exception $e) {
            Log::error('PayPal refund error: ' . $e->getMessage());
            return false;
        }
    }
}

