<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Congress;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show payment form
     */
    public function create(Congress $congress, Registration $registration)
    {
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        if ($registration->isPaid()) {
            return redirect()->route('registration.show', [$congress, $registration])
                ->with('info', 'Esta inscripción ya está pagada.');
        }

        return view('payment.create', compact('congress', 'registration'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Congress $congress, Registration $registration)
    {
        if ($registration->user_id !== auth()->id()) {
            abort(403);
        }

        if ($registration->isPaid()) {
            return back()->with('error', 'Esta inscripción ya está pagada.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:stripe,paypal,mercadopago,manual',
        ]);

        // Crear pago
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'user_id' => auth()->id(),
            'congress_id' => $congress->id,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'amount' => $registration->final_amount,
            'currency' => 'USD',
            'description' => "Inscripción a {$congress->title}",
        ]);

        // Actualizar registro con el pago
        $registration->update([
            'payment_id' => $payment->id,
            'payment_status' => 'processing',
        ]);

        // Aquí se integraría con la pasarela de pagos correspondiente
        // Por ahora, simulamos el proceso

        if ($validated['payment_method'] === 'manual') {
            // Pago manual (requiere aprobación del admin)
            return redirect()->route('payment.show', [$congress, $payment])
                ->with('info', 'Tu pago está pendiente de aprobación manual.');
        }

        // Para pasarelas reales, aquí se redirigiría al checkout
        // return redirect()->route('payment.process', [$congress, $payment]);

        // Simulación: marcar como completado (solo para desarrollo)
        if (config('app.debug')) {
            $this->completePayment($payment);
            return redirect()->route('payment.show', [$congress, $payment])
                ->with('success', 'Pago procesado exitosamente (modo desarrollo).');
        }

        return redirect()->route('payment.show', [$congress, $payment])
            ->with('info', 'Redirigiendo a la pasarela de pagos...');
    }

    /**
     * Show payment details
     */
    public function show(Congress $congress, Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load(['registration', 'transactions']);

        return view('payment.show', compact('congress', 'payment'));
    }

    /**
     * Webhook para procesar pagos (Stripe, PayPal, etc.)
     */
    public function webhook(Request $request, string $provider)
    {
        // Aquí se procesarían los webhooks de las pasarelas de pago
        // Por ejemplo: Stripe, PayPal, MercadoPago
        
        $payload = $request->all();
        
        // Validar y procesar según el proveedor
        // switch ($provider) {
        //     case 'stripe':
        //         // Procesar webhook de Stripe
        //         break;
        //     case 'paypal':
        //         // Procesar webhook de PayPal
        //         break;
        // }
        
        return response()->json(['status' => 'received']);
    }

    /**
     * Complete payment (helper method)
     */
    private function completePayment(Payment $payment): void
    {
        $payment->markAsPaid();

        // Actualizar registro
        if ($payment->registration) {
            $payment->registration->update([
                'payment_status' => 'completed',
            ]);
            $payment->registration->confirm();
        }

        // Crear transacción
        Transaction::create([
            'payment_id' => $payment->id,
            'congress_id' => $payment->congress_id,
            'transaction_type' => 'registration',
            'type' => 'debit',
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'description' => $payment->description,
        ]);
    }
}
