<div class="agenda-container">
    <!-- Filtros y controles -->
    <div class="agenda-controls mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Filtrar por Fecha</label>
                <input 
                    type="date" 
                    class="form-control" 
                    wire:model.live="filterDate"
                    value="{{ $filterDate }}"
                >
            </div>
            <div class="col-md-4">
                <label class="form-label">Filtrar por Simposio</label>
                <select class="form-select" wire:model.live="filterSymposium">
                    <option value="">Todos los simposios</option>
                    @foreach($symposia as $symposium)
                        <option value="{{ $symposium->id }}">{{ $symposium->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Vista</label>
                <div class="btn-group w-100" role="group">
                    <button 
                        type="button" 
                        class="btn {{ $viewMode === 'list' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setViewMode('list')"
                    >
                        <i class="fas fa-list"></i> Lista
                    </button>
                    <button 
                        type="button" 
                        class="btn {{ $viewMode === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setViewMode('calendar')"
                    >
                        <i class="fas fa-calendar"></i> Calendario
                    </button>
                    <button 
                        type="button" 
                        class="btn {{ $viewMode === 'grid' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="setViewMode('grid')"
                    >
                        <i class="fas fa-th"></i> Cuadrícula
                    </button>
                </div>
            </div>
        </div>
        @if($filterDate || $filterSymposium)
        <div class="mt-3">
            <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                <i class="fas fa-times"></i> Limpiar filtros
            </button>
        </div>
        @endif
    </div>

    <!-- Vista de Lista -->
    @if($viewMode === 'list')
        <div class="sessions-list">
            @forelse($sessions as $session)
                <div class="card mb-3 session-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    <a href="{{ route('virtual-sessions.show', [$congress, $session]) }}" class="text-decoration-none">
                                        {{ $session->title }}
                                    </a>
                                </h5>
                                @if($session->symposium)
                                    <span class="badge bg-secondary">{{ $session->symposium->title }}</span>
                                @endif
                                @if($session->paper)
                                    <span class="badge bg-info">Paper: {{ $session->paper->title }}</span>
                                @endif
                                <p class="card-text mt-2">{{ Str::limit($session->description, 150) }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                @if($session->scheduled_at)
                                    <div class="mb-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        <strong>{{ $session->scheduled_at->format('d/m/Y') }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-clock"></i>
                                        {{ $session->scheduled_at->format('H:i') }}
                                        @if($session->duration_minutes)
                                            - {{ $session->scheduled_at->copy()->addMinutes($session->duration_minutes)->format('H:i') }}
                                        @endif
                                    </div>
                                @endif
                                <div class="mb-2">
                                    <span class="badge 
                                        @if($session->status === 'live') bg-danger
                                        @elseif($session->status === 'completed') bg-success
                                        @elseif($session->status === 'scheduled') bg-primary
                                        @else bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-eye"></i> {{ $session->views_count }}
                                        <i class="fas fa-comments ms-2"></i> {{ $session->comments_count }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay sesiones programadas.
                </div>
            @endforelse

            {{ $sessions->links() }}
        </div>
    @endif

    <!-- Vista de Calendario -->
    @if($viewMode === 'calendar')
        <div class="sessions-calendar">
            @forelse($sessionsByDate as $date => $daySessions)
                <div class="calendar-day mb-4">
                    <h4 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-calendar-day"></i>
                        {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    </h4>
                    <div class="row">
                        @foreach($daySessions as $session)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('virtual-sessions.show', [$congress, $session]) }}">
                                                {{ $session->title }}
                                            </a>
                                        </h6>
                                        <p class="card-text">
                                            <i class="fas fa-clock"></i>
                                            {{ $session->scheduled_at->format('H:i') }}
                                            @if($session->duration_minutes)
                                                ({{ $session->duration_minutes }} min)
                                            @endif
                                        </p>
                                        <span class="badge 
                                            @if($session->status === 'live') bg-danger
                                            @elseif($session->status === 'completed') bg-success
                                            @else bg-primary
                                            @endif
                                        ">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay sesiones programadas.
                </div>
            @endforelse
        </div>
    @endif

    <!-- Vista de Cuadrícula -->
    @if($viewMode === 'grid')
        <div class="sessions-grid">
            <div class="row">
                @forelse($sessions as $session)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('virtual-sessions.show', [$congress, $session]) }}">
                                        {{ Str::limit($session->title, 50) }}
                                    </a>
                                </h6>
                                @if($session->scheduled_at)
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> {{ $session->scheduled_at->format('d/m/Y') }}<br>
                                            <i class="fas fa-clock"></i> {{ $session->scheduled_at->format('H:i') }}
                                        </small>
                                    </p>
                                @endif
                                <span class="badge 
                                    @if($session->status === 'live') bg-danger
                                    @elseif($session->status === 'completed') bg-success
                                    @else bg-primary
                                    @endif
                                ">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay sesiones programadas.
                        </div>
                    </div>
                @endforelse
            </div>
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@push('styles')
<style>
    .session-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .session-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .calendar-day {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
</style>
@endpush
