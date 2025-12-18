<?php

namespace App\Notifications;

use App\Models\SessionComment;
use App\Models\VirtualSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentOnPaperNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public VirtualSession $session,
        public SessionComment $comment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sessionTitle = $this->session->title;
        $commentAuthor = $this->comment->author_name;
        $commentType = $this->comment->isQuestion() ? 'pregunta' : 'comentario';

        return (new MailMessage)
            ->subject("Nuevo {$commentType} en tu ponencia: {$sessionTitle}")
            ->line("Has recibido un nuevo {$commentType} en tu ponencia.")
            ->line("**{$commentAuthor}** escribió:")
            ->line($this->comment->content)
            ->action('Ver comentario', route('virtual-sessions.show', [
                'congress' => $this->session->congress,
                'session' => $this->session
            ]))
            ->line('Gracias por usar nuestra plataforma!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session->id,
            'session_title' => $this->session->title,
            'comment_id' => $this->comment->id,
            'comment_author' => $this->comment->author_name,
            'comment_type' => $this->comment->type,
            'message' => "Nuevo {$this->comment->type} en tu ponencia: {$this->session->title}",
        ];
    }
}
