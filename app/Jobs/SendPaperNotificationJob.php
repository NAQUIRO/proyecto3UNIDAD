<?php

namespace App\Jobs;

use App\Mail\ReviewAssignmentMail;
use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaperNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ReviewAssignment $assignment;

    /**
     * Create a new job instance.
     */
    public function __construct(ReviewAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email al revisor
            Mail::to($this->assignment->reviewer->email)
                ->send(new ReviewAssignmentMail($this->assignment));
        } catch (\Exception $e) {
            \Log::error('Error sending paper notification: ' . $e->getMessage());
            throw $e; // Re-lanzar para que el job falle y se reintente
        }
    }
}
