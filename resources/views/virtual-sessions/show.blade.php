@extends('layouts.public')

@section('title', $session->title . ' - ' . $congress->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Información de la sesión -->
            <div class="card mb-4">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ $session->title }}</h1>
                </div>
                <div class="card-body">
                    @if($session->description)
                        <p class="lead">{{ $session->description }}</p>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p>
                                <i class="fas fa-calendar-alt"></i> 
                                <strong>Fecha:</strong> 
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('d/m/Y') }}
                                @else
                                    Por definir
                                @endif
                            </p>
                            <p>
                                <i class="fas fa-clock"></i> 
                                <strong>Hora:</strong> 
                                @if($session->scheduled_at)
                                    {{ $session->scheduled_at->format('H:i') }}
                                    @if($session->duration_minutes)
                                        ({{ $session->duration_minutes }} minutos)
                                    @endif
                                @else
                                    Por definir
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="fas fa-tag"></i> 
                                <strong>Estado:</strong> 
                                <span class="badge 
                                    @if($session->status === 'live') bg-danger
                                    @elseif($session->status === 'completed') bg-success
                                    @elseif($session->status === 'scheduled') bg-primary
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </p>
                            @if($session->symposium)
                                <p>
                                    <i class="fas fa-users"></i> 
                                    <strong>Simposio:</strong> 
                                    {{ $session->symposium->title }}
                                </p>
                            @endif
                            @if($session->paper)
                                <p>
                                    <i class="fas fa-file-alt"></i> 
                                    <strong>Paper:</strong> 
                                    <a href="{{ route('congress.papers.show', [$congress, $session->paper]) }}">
                                        {{ $session->paper->title }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-info">
                            <i class="fas fa-eye"></i> {{ $session->views_count }} vistas
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-comments"></i> {{ $session->comments_count }} comentarios
                        </span>
                    </div>
                </div>
            </div>

            <!-- Video -->
            @if($session->video_url)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-video"></i> Video de la Sesión</h5>
                    </div>
                    <div class="card-body">
                        @if($session->video_provider === 'youtube' && $session->video_id)
                            <div class="ratio ratio-16x9">
                                <iframe 
                                    src="{{ $session->video_embed_url }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @elseif($session->video_provider === 'vimeo' && $session->video_id)
                            <div class="ratio ratio-16x9">
                                <iframe 
                                    src="{{ $session->video_embed_url }}" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <a href="{{ $session->video_url }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> Ver video
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Comentarios (Livewire Component) -->
            @auth
                @livewire('session.session-comments', ['session' => $session])
            @else
                <div class="card">
                    <div class="card-body text-center">
                        <p>Debes <a href="{{ route('login') }}">iniciar sesión</a> para comentar.</p>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información</h5>
                </div>
                <div class="card-body">
                    @if($session->paper && $session->paper->author)
                        <div class="mb-3">
                            <h6>Autor</h6>
                            <p class="mb-0">
                                <strong>{{ $session->paper->author->name }}</strong><br>
                                <small class="text-muted">{{ $session->paper->author->email }}</small>
                            </p>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6>Acciones</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('agenda', $congress) }}" class="btn btn-outline-primary">
                                <i class="fas fa-calendar"></i> Ver Agenda Completa
                            </a>
                            @if($session->paper)
                                <a href="{{ route('congress.papers.show', [$congress, $session->paper]) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-file-alt"></i> Ver Paper
                                </a>
                            @endif
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole(['Super Admin', 'Admin']))
                            <div class="mb-3">
                                <h6>Administración</h6>
                                <div class="d-grid gap-2">
                                    @if($session->status === 'scheduled')
                                        <form action="{{ route('congresses.virtual-sessions.start', [$congress, $session]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-play"></i> Iniciar Sesión
                                            </button>
                                        </form>
                                    @endif
                                    @if($session->status === 'live')
                                        <form action="{{ route('congresses.virtual-sessions.end', [$congress, $session]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-stop"></i> Finalizar Sesión
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('congresses.virtual-sessions.edit', [$congress, $session]) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Editar Sesión
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

