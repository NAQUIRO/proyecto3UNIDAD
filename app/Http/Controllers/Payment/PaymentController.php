<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\ReceiptService;
use App\Models\Congress;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected ReceiptService $receiptService;

    public function __construct(PaymentService $paymentService, ReceiptService $receiptService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
        $this->receiptService = $receiptService;
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
            return redirect()->route('congress.registration.show', [$congress, $registration])
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
            'currency' => $registration->congress->settings['currency'] ?? 'USD',
            'description' => "Inscripción a {$congress->title}",
        ]);

        // Actualizar registro con el pago
        $registration->update([
            'payment_id' => $payment->id,
            'payment_status' => 'processing',
        ]);

        // Procesar pago usando el servicio
        if ($validated['payment_method'] === 'manual') {
            // Pago manual (requiere aprobación del admin)
            return redirect()->route('congress.payment.show', [$congress, $payment])
                ->with('info', 'Tu pago está pendiente de aprobación manual.');
        }

        // Para pasarelas reales, redirigir al checkout
        try {
            $checkoutUrl = $this->paymentService->process($payment);
            return redirect($checkoutUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Show payment details
     */
    public function show(Congress $congress, Payment $payment)
    {
        if ($payment->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $payment->load(['registration', 'transactions', 'user', 'congress']);

        return view('payment.show', compact('congress', 'payment'));
    }

    /**
     * Download receipt
     */
    public function downloadReceipt(Congress $congress, Payment $payment)
    {
        if ($payment->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$payment->isCompleted()) {
            return back()->with('error', 'Solo se pueden descargar recibos de pagos completados.');
        }

        return $this->receiptService->downloadReceipt($payment);
    }

    /**
     * Generate receipt
     */
    public function generateReceipt(Congress $congress, Payment $payment)
    {
        if ($payment->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$payment->isCompleted()) {
            return back()->with('error', 'Solo se pueden generar recibos de pagos completados.');
        }

        $receiptUrl = $this->receiptService->generateReceipt($payment);

        return redirect($receiptUrl);
    }

    /**
     * Webhook para procesar pagos (Stripe, PayPal, etc.)
     */
    public function webhook(Request $request, string $provider)
    {
        try {
            $payment = $this->paymentService->handleWebhook($provider, $request->all());
            
            // Si el pago fue completado, generar recibo y actualizar registro
            if ($payment->isCompleted()) {
                // Generar recibo
                $this->receiptService->generateReceipt($payment);

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

                // Despachar job de notificación
                \App\Jobs\SendPaymentCompletedNotificationJob::dispatch($payment);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            \Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
