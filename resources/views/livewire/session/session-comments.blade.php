<div class="session-comments" wire:poll.{{ $autoRefresh ? $refreshInterval : 'off' }}.ms>
    <!-- Formulario de comentario -->
    <div class="comment-form mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comment"></i> 
                    {{ $parentId ? 'Responder comentario' : 'Agregar comentario' }}
                </h5>
            </div>
            <div class="card-body">
                @if(session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($parentId)
                    <div class="alert alert-info">
                        <i class="fas fa-reply"></i> Respondiendo a un comentario
                        <button type="button" class="btn btn-sm btn-outline-secondary float-end" wire:click="cancelReply">
                            Cancelar
                        </button>
                    </div>
                @endif

                <form wire:submit="submitComment">
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="type_comment" value="comment" wire:model="type">
                            <label class="btn btn-outline-primary" for="type_comment">
                                <i class="fas fa-comment"></i> Comentario
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type_question" value="question" wire:model="type">
                            <label class="btn btn-outline-primary" for="type_question">
                                <i class="fas fa-question-circle"></i> Pregunta
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <textarea 
                            class="form-control @error('content') is-invalid @enderror" 
                            id="content"
                            rows="4" 
                            wire:model="content"
                            placeholder="Escribe tu comentario o pregunta aquí..."
                        ></textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleAutoRefresh">
                            <i class="fas fa-sync-alt"></i> 
                            Auto-actualizar: {{ $autoRefresh ? 'ON' : 'OFF' }}
                        </button>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> 
                            {{ $type === 'question' ? 'Enviar Pregunta' : 'Publicar Comentario' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preguntas destacadas -->
    @if($questions->count() > 0)
        <div class="questions-section mb-4">
            <h5 class="mb-3">
                <i class="fas fa-question-circle text-warning"></i> Preguntas ({{ $questions->count() }})
            </h5>
            <div class="list-group">
                @foreach($questions as $question)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $question->author_name }}
                                    @if($question->is_answered)
                                        <span class="badge bg-success">Respondida</span>
                                    @endif
                                </h6>
                                <p class="mb-1">{{ $question->content }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $question->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div>
                                <button 
                                    class="btn btn-sm btn-outline-primary" 
                                    wire:click="likeComment({{ $question->id }})"
                                    title="Me gusta"
                                >
                                    <i class="fas fa-thumbs-up"></i> {{ $question->likes_count }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Comentarios -->
    <div class="comments-section">
        <h5 class="mb-3">
            <i class="fas fa-comments"></i> Comentarios ({{ $session->comments_count }})
        </h5>

        @forelse($comments as $comment)
            <div class="card mb-3 comment-item" id="comment-{{ $comment->id }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-0">
                                {{ $comment->author_name }}
                                @if($comment->type === 'question')
                                    <span class="badge bg-warning text-dark">Pregunta</span>
                                @endif
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $comment->created_at->diffForHumans() }}
                            </small>
                        </div>
                        <div>
                            <button 
                                class="btn btn-sm btn-outline-primary" 
                                wire:click="likeComment({{ $comment->id }})"
                                title="Me gusta"
                            >
                                <i class="fas fa-thumbs-up"></i> {{ $comment->likes_count }}
                            </button>
                            <button 
                                class="btn btn-sm btn-outline-secondary" 
                                wire:click="replyTo({{ $comment->id }})"
                                title="Responder"
                            >
                                <i class="fas fa-reply"></i>
                            </button>
                        </div>
                    </div>
                    <p class="mb-0">{{ $comment->content }}</p>

                    <!-- Respuestas -->
                    @if($comment->replies->count() > 0)
                        <div class="replies mt-3 ms-4 border-start ps-3">
                            @foreach($comment->replies as $reply)
                                <div class="reply-item mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $reply->author_name }}</strong>
                                            <small class="text-muted ms-2">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <button 
                                            class="btn btn-sm btn-outline-primary" 
                                            wire:click="likeComment({{ $reply->id }})"
                                        >
                                            <i class="fas fa-thumbs-up"></i> {{ $reply->likes_count }}
                                        </button>
                                    </div>
                                    <p class="mb-0">{{ $reply->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aún no hay comentarios. ¡Sé el primero en comentar!
            </div>
        @endforelse

        {{ $comments->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('focusCommentInput', () => {
            document.getElementById('content')?.focus();
        });

        Livewire.on('commentLiked', (commentId) => {
            // Opcional: animación visual
            const commentEl = document.getElementById('comment-' + commentId);
            if (commentEl) {
                commentEl.style.transition = 'transform 0.2s';
                commentEl.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    commentEl.style.transform = 'scale(1)';
                }, 200);
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .comment-item {
        transition: all 0.3s ease;
    }
    .comment-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .replies {
        border-left: 3px solid #e0e0e0 !important;
    }
    .reply-item {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }
</style>
@endpush
