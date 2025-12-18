<?php

namespace App\Jobs;

use App\Mail\CampaignEmail;
use App\Models\EmailCampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public EmailCampaignRecipient $recipient;
    public string $subject;
    public string $content;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailCampaignRecipient $recipient, string $subject, string $content)
    {
        $this->recipient = $recipient;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->recipient->email, $this->recipient->name)
                ->send(new CampaignEmail($this->subject, $this->content));

            $this->recipient->markAsSent();
        } catch (\Exception $e) {
            Log::error('Bulk email error: ' . $e->getMessage(), [
                'recipient_id' => $this->recipient->id,
                'email' => $this->recipient->email,
            ]);
            
            $this->recipient->markAsFailed($e->getMessage());
            throw $e; // Re-lanzar para que el job falle y se reintente
        }
    }
}
