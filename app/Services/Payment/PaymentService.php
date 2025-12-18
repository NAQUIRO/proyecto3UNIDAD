<?php

namespace App\Services\Payment;

use App\Contracts\PaymentProviderInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected array $providers = [];

    public function __construct()
    {
        // Registrar proveedores disponibles
        $this->providers = [
            'stripe' => StripePaymentService::class,
            'paypal' => PayPalPaymentService::class,
            'mercadopago' => MercadoPagoPaymentService::class,
            'manual' => ManualPaymentService::class,
        ];
    }

    /**
     * Obtener el servicio del proveedor
     */
    public function getProvider(string $method): PaymentProviderInterface
    {
        if (!isset($this->providers[$method])) {
            throw new \Exception("Payment provider '{$method}' not found");
        }

        $providerClass = $this->providers[$method];
        return app($providerClass);
    }

    /**
     * Procesar un pago
     */
    public function process(Payment $payment): string
    {
        $provider = $this->getProvider($payment->payment_method);
        return $provider->processPayment($payment);
    }

    /**
     * Verificar un pago
     */
    public function verify(Payment $payment): array
    {
        if (!$payment->payment_provider_id) {
            throw new \Exception('Payment provider ID not found');
        }

        $provider = $this->getProvider($payment->payment_method);
        return $provider->verifyPayment($payment->payment_provider_id);
    }

    /**
     * Procesar webhook
     */
    public function handleWebhook(string $provider, array $payload): Payment
    {
        $service = $this->getProvider($provider);
        return $service->handleWebhook($payload);
    }

    /**
     * Reembolsar un pago
     */
    public function refund(Payment $payment, ?float $amount = null): bool
    {
        try {
            $provider = $this->getProvider($payment->payment_method);
            return $provider->refund($payment, $amount);
        } catch (\Exception $e) {
            Log::error('Refund error: ' . $e->getMessage());
            return false;
        }
    }
}

