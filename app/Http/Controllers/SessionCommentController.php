<?php

namespace App\Http\Controllers;

use App\Models\Congress;
use App\Models\SessionComment;
use App\Models\VirtualSession;
use App\Notifications\CommentOnPaperNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SessionCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created comment
     */
    public function store(Request $request, Congress $congress, VirtualSession $session)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'required|in:comment,question',
            'parent_id' => 'nullable|exists:session_comments,id',
        ]);

        $comment = SessionComment::create([
            'session_id' => $session->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->user()->name,
            'author_email' => auth()->user()->email,
            'content' => $validated['content'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
            'is_approved' => true, // En producción, podría requerir moderación
        ]);

        // Incrementar contador de comentarios
        $session->incrementComments();

        // Notificar al autor del paper si existe
        if ($session->paper && $session->paper->author_id !== auth()->id()) {
            $session->paper->author->notify(
                new CommentOnPaperNotification($session, $comment)
            );
        }

        return back()->with('success', $comment->isQuestion() ? 'Pregunta enviada exitosamente.' : 'Comentario publicado exitosamente.');
    }

    /**
     * Update the specified comment
     */
    public function update(Request $request, Congress $congress, VirtualSession $session, SessionComment $comment)
    {
        // Solo el autor puede editar su comentario
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment->update($validated);

        return back()->with('success', 'Comentario actualizado exitosamente.');
    }

    /**
     * Remove the specified comment
     */
    public function destroy(Congress $congress, VirtualSession $session, SessionComment $comment)
    {
        // Solo el autor o un admin pueden eliminar
        if ($comment->user_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        $comment->delete();

        // Decrementar contador
        $session->decrement('comments_count');
        if ($session->comments_count < 0) {
            $session->update(['comments_count' => 0]);
        }

        return back()->with('success', 'Comentario eliminado exitosamente.');
    }

    /**
     * Mark question as answered
     */
    public function markAnswered(Congress $congress, VirtualSession $session, SessionComment $comment)
    {
        // Solo el autor del paper o un admin pueden marcar como respondida
        if ($session->paper && $session->paper->author_id !== auth()->id() && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403);
        }

        if (!$comment->isQuestion()) {
            return back()->with('error', 'Solo las preguntas pueden marcarse como respondidas.');
        }

        $comment->markAsAnswered();

        return back()->with('success', 'Pregunta marcada como respondida.');
    }

    /**
     * Like a comment
     */
    public function like(Congress $congress, VirtualSession $session, SessionComment $comment)
    {
        // Aquí se podría implementar un sistema de likes más complejo con tabla pivot
        // Por ahora, simplemente incrementamos el contador
        $comment->incrementLikes();

        return response()->json([
            'success' => true,
            'likes_count' => $comment->fresh()->likes_count,
        ]);
    }
}
