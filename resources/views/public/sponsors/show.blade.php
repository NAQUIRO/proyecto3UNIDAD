@extends('layouts.public')

@section('title', $sponsor->name . ' - Patrocinadores')

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('patrocinadores.index') }}">Patrocinadores</a></li>
                <li class="breadcrumb-item active">{{ $sponsor->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-4 mb-4">
                <div class="sponsor-profile-card">
                    <div class="sponsor-profile-logo">
                        <img 
                            src="{{ $sponsor->logo_url ?? asset('img/default-logo.png') }}" 
                            alt="{{ $sponsor->name }}" 
                            class="img-fluid"
                            loading="lazy"
                            onerror="this.src='{{ asset('img/default-logo.png') }}'"
                        >
                    </div>
                    <div class="sponsor-type-badge sponsor-{{ $sponsor->sponsor_type }}">
                        <i class="fas fa-{{ $sponsor->sponsor_type === 'platinum' ? 'crown' : ($sponsor->sponsor_type === 'gold' ? 'medal' : ($sponsor->sponsor_type === 'silver' ? 'award' : ($sponsor->sponsor_type === 'bronze' ? 'certificate' : 'handshake'))) }}"></i>
                        {{ $sponsor->sponsor_type_label }}
                    </div>
                    <div class="sponsor-profile-info mt-4">
                        <h2 class="sponsor-profile-name">{{ $sponsor->name }}</h2>
                        
                        @if($sponsor->website)
                            <p class="sponsor-profile-detail">
                                <i class="fas fa-link text-primary"></i>
                                <a href="{{ $sponsor->website }}" target="_blank" rel="noopener">{{ $sponsor->website }}</a>
                            </p>
                        @endif

                        @if($sponsor->email)
                            <p class="sponsor-profile-detail">
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:{{ $sponsor->email }}">{{ $sponsor->email }}</a>
                            </p>
                        @endif

                        @if($sponsor->phone)
                            <p class="sponsor-profile-detail">
                                <i class="fas fa-phone text-primary"></i>
                                {{ $sponsor->phone }}
                            </p>
                        @endif

                        @if($sponsor->website)
                            <div class="mt-4">
                                <a href="{{ $sponsor->website }}" target="_blank" rel="noopener" class="btn btn-primary w-100">
                                    <i class="fas fa-external-link-alt"></i> Visitar sitio web
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-lg-8">
                <!-- Descripción -->
                @if($sponsor->description)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-info-circle"></i> Acerca de
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="sponsor-description-text">{{ $sponsor->description }}</p>
                        </div>
                    </div>
                @endif

                <!-- Congreso -->
                @if($sponsor->congress)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                <i class="fas fa-calendar-alt"></i> Congreso
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5><a href="{{ route('congress.home', $sponsor->congress->slug) }}">{{ $sponsor->congress->title }}</a></h5>
                            @if($sponsor->congress->description)
                                <p>{{ Str::limit($sponsor->congress->description, 200) }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Patrocinadores Relacionados -->
        @if($relatedSponsors->count() > 0)
            <div class="related-sponsors mt-5">
                <h3 class="mb-4">
                    <i class="fas fa-handshake"></i> Otros Patrocinadores {{ $sponsor->sponsor_type_label }}
                </h3>
                <div class="row g-4">
                    @foreach($relatedSponsors as $related)
                        <div class="col-md-3">
                            <div class="sponsor-card-mini">
                                <a href="{{ route('patrocinadores.show', $related) }}">
                                    <div class="sponsor-mini-logo-wrapper">
                                        <img 
                                            src="{{ $related->logo_url ?? asset('img/default-logo.png') }}" 
                                            alt="{{ $related->name }}" 
                                            class="sponsor-mini-logo"
                                            loading="lazy"
                                        >
                                    </div>
                                    <h5 class="mt-2">{{ $related->name }}</h5>
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
    .sponsor-profile-card {
        position: sticky;
        top: 20px;
        text-align: center;
    }

    .sponsor-profile-logo {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .sponsor-profile-logo img {
        max-width: 100%;
        max-height: 200px;
        object-fit: contain;
    }

    .sponsor-type-badge {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: bold;
        color: white;
        margin-bottom: 20px;
    }

    .sponsor-type-badge.sponsor-platinum {
        background: linear-gradient(135deg, #e5e4e2 0%, #c0c0c0 100%);
    }

    .sponsor-type-badge.sponsor-gold {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    }

    .sponsor-type-badge.sponsor-silver {
        background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
    }

    .sponsor-type-badge.sponsor-bronze {
        background: linear-gradient(135deg, #cd7f32 0%, #b87333 100%);
    }

    .sponsor-type-badge.sponsor-partner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .sponsor-profile-name {
        font-size: 1.75rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }

    .sponsor-profile-detail {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: center;
    }

    .sponsor-profile-detail i {
        width: 20px;
    }

    .sponsor-description-text {
        line-height: 1.8;
        color: #555;
        font-size: 1.05rem;
    }

    .sponsor-card-mini {
        text-align: center;
        transition: transform 0.3s ease;
    }

    .sponsor-card-mini:hover {
        transform: translateY(-5px);
    }

    .sponsor-card-mini a {
        text-decoration: none;
        color: inherit;
    }

    .sponsor-mini-logo-wrapper {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sponsor-mini-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>
@endpush

