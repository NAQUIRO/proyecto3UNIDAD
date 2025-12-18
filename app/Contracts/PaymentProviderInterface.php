<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentProviderInterface
{
    /**
     * Crear un pago en la pasarela
     */
    public function createPayment(Payment $payment): array;

    /**
     * Procesar el pago
     */
    public function processPayment(Payment $payment): string; // Retorna URL de checkout

    /**
     * Verificar el estado de un pago
     */
    public function verifyPayment(string $paymentId): array;

    /**
     * Procesar webhook del proveedor
     */
    public function handleWebhook(array $payload): Payment;

    /**
     * Reembolsar un pago
     */
    public function refund(Payment $payment, ?float $amount = null): bool;
}

