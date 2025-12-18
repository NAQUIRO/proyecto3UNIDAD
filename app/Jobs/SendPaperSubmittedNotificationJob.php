<?php

namespace App\Jobs;

use App\Mail\PaperSubmittedMail;
use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaperSubmittedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Paper $paper;

    /**
     * Create a new job instance.
     */
    public function __construct(Paper $paper)
    {
        $this->paper = $paper;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Enviar email al autor
            Mail::to($this->paper->author->email)
                ->send(new PaperSubmittedMail($this->paper));

            // Notificar a los administradores del congreso
            $congress = $this->paper->congress;
            $admins = \App\Models\User::role(['Super Admin', 'Admin'])
                ->whereHas('congresses', function ($query) use ($congress) {
                    $query->where('congress_id', $congress->id);
                })
                ->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new PaperSubmittedMail($this->paper));
            }
        } catch (\Exception $e) {
            \Log::error('Error sending paper submitted notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
