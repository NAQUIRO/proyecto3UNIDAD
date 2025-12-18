<?php

namespace App\Jobs;

use App\Mail\ReviewCompletedMail;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReviewCompletedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Review $review;

    /**
     * Create a new job instance.
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email al autor del paper
            Mail::to($this->review->paper->author->email)
                ->send(new ReviewCompletedMail($this->review));

            // También notificar a los administradores del congreso
            $congress = $this->review->paper->congress;
            $admins = \App\Models\User::role(['Super Admin', 'Admin'])
                ->whereHas('congresses', function ($query) use ($congress) {
                    $query->where('congress_id', $congress->id);
                })
                ->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new ReviewCompletedMail($this->review));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending review completed notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
