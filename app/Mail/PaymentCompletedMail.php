<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pago Completado - ' . $this->payment->congress->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-completed',
            with: [
                'payment' => $this->payment,
                'congress' => $this->payment->congress,
                'user' => $this->payment->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Adjuntar recibo PDF si existe
        if ($this->payment->receipt_url) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromPath(
                    storage_path('app/public/' . str_replace('/storage/', '', $this->payment->receipt_url))
                )->as('recibo.pdf'),
            ];
        }

        return [];
    }
}
