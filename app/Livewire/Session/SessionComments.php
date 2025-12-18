<?php

namespace App\Livewire\Session;

use App\Models\SessionComment;
use App\Models\VirtualSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SessionComments extends Component
{
    use WithPagination;

    public VirtualSession $session;
    public string $content = '';
    public string $type = 'comment';
    public ?int $parentId = null;
    public bool $autoRefresh = true;
    public int $refreshInterval = 5000; // 5 segundos

    protected $listeners = ['commentAdded' => '$refresh'];

    public function mount(VirtualSession $session)
    {
        $this->session = $session;
    }

    public function submitComment()
    {
        $this->validate([
            'content' => 'required|string|max:2000|min:3',
            'type' => 'required|in:comment,question',
        ]);

        SessionComment::create([
            'session_id' => $this->session->id,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()->name,
            'author_email' => Auth::user()->email,
            'content' => $this->content,
            'type' => $this->type,
            'parent_id' => $this->parentId,
            'is_approved' => true,
        ]);

        // Incrementar contador
        $this->session->incrementComments();

        // Limpiar formulario
        $this->content = '';
        $this->type = 'comment';
        $this->parentId = null;

        // Emitir evento para actualizar otros componentes
        $this->dispatch('commentAdded');

        session()->flash('message', $this->type === 'question' ? 'Pregunta enviada exitosamente.' : 'Comentario publicado exitosamente.');
    }

    public function replyTo(int $commentId)
    {
        $this->parentId = $commentId;
        $this->dispatch('focusCommentInput');
    }

    public function cancelReply()
    {
        $this->parentId = null;
    }

    public function likeComment(int $commentId)
    {
        $comment = SessionComment::findOrFail($commentId);
        $comment->incrementLikes();
        
        $this->dispatch('commentLiked', $commentId);
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function render()
    {
        // Recargar la sesión para obtener los últimos comentarios
        $this->session->refresh();

        $comments = SessionComment::where('session_id', $this->session->id)
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $questions = SessionComment::where('session_id', $this->session->id)
            ->where('type', 'question')
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.session.session-comments', [
            'comments' => $comments,
            'questions' => $questions,
        ]);
    }
}
