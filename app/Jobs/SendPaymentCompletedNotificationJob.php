<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentCompletedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payment $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email al usuario con el recibo
            if ($this->payment->user) {
                Mail::to($this->payment->user->email)
                    ->send(new \App\Mail\PaymentCompletedMail($this->payment));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending payment completed notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
