@extends('layouts.public')

@section('title', 'Mi Dashboard - EventHub')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .dashboard-container {
        min-height: 100vh;
        background: #f5f7fa;
        padding: 30px 0;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .welcome-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .event-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .event-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .event-card-body {
        padding: 20px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #ddd;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Header del Dashboard -->
        <div class="dashboard-header">
            <h1><i class="fas fa-user-circle"></i> Mi Dashboard</h1>
            <p class="mb-0">Bienvenido, {{ Auth::user()->name }}</p>
        </div>

        <!-- Mensajes Flash -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tarjeta de Bienvenida -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-star text-warning"></i> ¡Bienvenido a EventHub!</h3>
                    <p class="mb-0">Explora los congresos disponibles e inscríbete en los que más te interesen. Desde aquí puedes gestionar tus inscripciones y mantenerte al día con las últimas novedades.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('public.congresses.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search"></i> Ver Todos los Congresos
                    </a>
                </div>
            </div>
        </div>

        <!-- Congresos Disponibles -->
        <div class="mb-4">
            <h2 class="section-title">
                <i class="fas fa-calendar-check"></i> Congresos Disponibles
            </h2>
            @php
                $featuredCongresses = \App\Models\Congress::where('status', 'published')
                    ->with('thematicAreas')
                    ->orderBy('start_date', 'asc')
                    ->limit(6)
                    ->get();
            @endphp

            @if($featuredCongresses->count() > 0)
                <div class="row g-4">
                    @foreach($featuredCongresses as $congress)
                        <div class="col-md-4">
                            <div class="event-card">
                                @if($congress->banner)
                                    <img src="{{ asset('storage/' . $congress->banner) }}" alt="{{ $congress->title }}">
                                @else
                                    <img src="{{ asset('img/eventos/evento1.jpg') }}" alt="{{ $congress->title }}">
                                @endif
                                <div class="event-card-body">
                                    <h5>{{ $congress->title }}</h5>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-calendar"></i>
                                        {{ \Carbon\Carbon::parse($congress->start_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($congress->end_date)->format('d/m/Y') }}
                                    </p>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $congress->location ?? 'Virtual' }}
                                    </p>
                                    <p class="mb-3">
                                        @foreach($congress->thematicAreas as $area)
                                            <span class="badge bg-primary">{{ $area->name }}</span>
                                        @endforeach
                                    </p>
                                    <a href="{{ route('public.congresses.show', $congress->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-info-circle"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('public.congresses.index') }}" class="btn btn-primary">
                        Ver Todos los Congresos <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div class="empty-state bg-white rounded p-5">
                    <i class="fas fa-calendar-times"></i>
                    <h4>No hay congresos disponibles</h4>
                    <p>Los congresos aparecerán aquí cuando estén disponibles.</p>
                </div>
            @endif
        </div>

        <!-- Mis Inscripciones -->
        <div class="bg-white rounded p-4 shadow-sm">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i> Mis Inscripciones
            </h2>
            @php
                $myRegistrations = \App\Models\Registration::where('user_id', Auth::id())
                    ->with('congress')
                    ->orderBy('registered_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($myRegistrations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Congreso</th>
                                <th>Fecha de Inscripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myRegistrations as $registration)
                                <tr>
                                    <td>{{ $registration->congress->title }}</td>
                                    <td>
                                        @if($registration->registered_at)
                                            {{ \Carbon\Carbon::parse($registration->registered_at)->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($registration->status === 'confirmed')
                                            <span class="badge bg-success">Confirmada</span>
                                        @elseif($registration->status === 'pending')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $registration->status ?? 'Pendiente' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('congress.registration.show', ['congress' => $registration->congress->id, 'registration' => $registration->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No tienes inscripciones aún</h4>
                    <p>Cuando te inscribas a un congreso, aparecerá aquí.</p>
                    <a href="{{ route('public.congresses.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> Explorar Congresos
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
