@extends('layouts.public')

@section('title', $speaker->name . ' - Ponentes')

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ponentes.index') }}">Ponentes</a></li>
                <li class="breadcrumb-item active">{{ $speaker->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-4 mb-4">
                <div class="speaker-profile-card">
                    <div class="speaker-profile-image">
                        <img 
                            src="{{ $speaker->photo_url ?? asset('img/default-speaker.jpg') }}" 
                            alt="{{ $speaker->name }}" 
                            class="img-fluid rounded"
                            loading="lazy"
                            onerror="this.src='{{ asset('img/default-speaker.jpg') }}'"
                        >
                        @if($speaker->is_featured)
                            <span class="featured-badge-large">
                                <i class="fas fa-star"></i> Destacado
                            </span>
                        @endif
                    </div>
                    <div class="speaker-profile-info mt-4">
                        <h2 class="speaker-profile-name">{{ $speaker->name }}</h2>
                        
                        @if($speaker->position)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-briefcase text-primary"></i>
                                <strong>{{ $speaker->position }}</strong>
                            </p>
                        @endif

                        @if($speaker->institution)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-building text-primary"></i>
                                {{ $speaker->institution }}
                            </p>
                        @endif

                        @if($speaker->specialization)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-tag text-primary"></i>
                                {{ $speaker->specialization }}
                            </p>
                        @endif

                        @if($speaker->country)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-globe text-primary"></i>
                                {{ $speaker->country }}
                            </p>
                        @endif

                        @if($speaker->email)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:{{ $speaker->email }}">{{ $speaker->email }}</a>
                            </p>
                        @endif

                        @if($speaker->website)
                            <p class="speaker-profile-detail">
                                <i class="fas fa-link text-primary"></i>
                                <a href="{{ $speaker->website }}" target="_blank" rel="noopener">Sitio web</a>
                            </p>
                        @endif

                        @if($speaker->social_media)
                            <div class="social-media mt-3">
                                @if(isset($speaker->social_media['linkedin']))
                                    <a href="{{ $speaker->social_media['linkedin'] }}" target="_blank" class="social-link">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                @endif
                                @if(isset($speaker->social_media['twitter']))
                                    <a href="{{ $speaker->social_media['twitter'] }}" target="_blank" class="social-link">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                @endif
                                @if(isset($speaker->social_media['facebook']))
                                    <a href="{{ $speaker->social_media['facebook'] }}" target="_blank" class="social-link">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-lg-8">
                <!-- Biografía -->
                @if($speaker->bio)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-user-circle"></i> Biografía
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="speaker-bio-text">{{ $speaker->bio }}</p>
                        </div>
                    </div>
                @endif

                <!-- Papers/Presentaciones -->
                @if($speaker->papers->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-file-alt"></i> Presentaciones
                                <span class="badge bg-primary ms-2">{{ $speaker->papers->count() }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                @foreach($speaker->papers as $paper)
                                    <div class="list-group-item">
                                        <h5 class="mb-1">
                                            <a href="{{ route('congress.papers.show', [$paper->congress, $paper]) }}">
                                                {{ $paper->title }}
                                            </a>
                                        </h5>
                                        <p class="mb-1 text-muted">{{ Str::limit($paper->abstract, 150) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $paper->created_at->format('d/m/Y') }}
                                            @if($paper->congress)
                                                | <i class="fas fa-building"></i> {{ $paper->congress->title }}
                                            @endif
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Congreso -->
                @if($speaker->congress)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-calendar-alt"></i> Congreso
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5><a href="{{ route('congress.home', $speaker->congress->slug) }}">{{ $speaker->congress->title }}</a></h5>
                            @if($speaker->congress->description)
                                <p>{{ Str::limit($speaker->congress->description, 200) }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Ponentes Relacionados -->
        @if($relatedSpeakers->count() > 0)
            <div class="related-speakers mt-5">
                <h3 class="mb-4">
                    <i class="fas fa-users"></i> Otros Ponentes
                </h3>
                <div class="row g-4">
                    @foreach($relatedSpeakers as $related)
                        <div class="col-md-3">
                            <div class="speaker-card-mini">
                                <a href="{{ route('ponentes.show', $related) }}">
                                    <img 
                                        src="{{ $related->photo_url ?? asset('img/default-speaker.jpg') }}" 
                                        alt="{{ $related->name }}" 
                                        class="speaker-mini-image"
                                        loading="lazy"
                                    >
                                    <h5 class="mt-2">{{ $related->name }}</h5>
                                    @if($related->position)
                                        <p class="text-muted small">{{ $related->position }}</p>
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    .speaker-profile-card {
        position: sticky;
        top: 20px;
    }

    .speaker-profile-image {
        position: relative;
        text-align: center;
    }

    .speaker-profile-image img {
        width: 100%;
        max-width: 300px;
        height: auto;
        border: 5px solid #667eea;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .featured-badge-large {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffc107;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .speaker-profile-name {
        font-size: 1.75rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }

    .speaker-profile-detail {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .speaker-profile-detail i {
        width: 20px;
    }

    .speaker-bio-text {
        line-height: 1.8;
        color: #555;
        font-size: 1.05rem;
    }

    .social-media {
        display: flex;
        gap: 10px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #764ba2;
        transform: translateY(-3px);
        color: white;
    }

    .speaker-card-mini {
        text-align: center;
        transition: transform 0.3s ease;
    }

    .speaker-card-mini:hover {
        transform: translateY(-5px);
    }

    .speaker-card-mini a {
        text-decoration: none;
        color: inherit;
    }

    .speaker-mini-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
        border: 3px solid #667eea;
    }
</style>
@endpush

