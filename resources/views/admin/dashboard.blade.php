@extends('layouts.public')

@section('title', 'Dashboard Admin - EventHub')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .stat-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    .stat-label {
        font-size: 1rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .quick-actions {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-top: 30px;
    }
    .action-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        transition: background 0.3s;
    }
    .action-card:hover {
        background: #e9ecef;
    }
    .action-card a {
        text-decoration: none;
        color: #333;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .action-card i {
        font-size: 1.5rem;
        color: #667eea;
    }
    .chart-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .chart-header {
        padding: 20px;
        color: white;
        font-weight: 600;
    }
    .chart-header.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .chart-header.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    .chart-body {
        padding: 20px;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Header del Dashboard -->
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Panel de Administración</h1>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_congresses'] }}</div>
                    <div class="stat-label">Congresos Totales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">{{ $stats['published_congresses'] }}</div>
                    <div class="stat-label">Congresos Publicados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-number">{{ $stats['draft_congresses'] }}</div>
                    <div class="stat-label">Borradores</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_thematic_areas'] }}</div>
                    <div class="stat-label">Áreas Temáticas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-secondary">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="stat-number">{{ $stats['finished_congresses'] }}</div>
                    <div class="stat-label">Congresos Finalizados</div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="quick-actions">
            <h3 class="mb-4"><i class="fas fa-bolt"></i> Accesos Rápidos</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="action-card">
                        <a href="{{ route('admin.congresses.index') }}">
                            <div>
                                <h5><i class="fas fa-calendar-plus"></i> Gestión de Congresos</h5>
                                <p class="mb-0 text-muted">Crear, editar y eliminar congresos</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card">
                        <a href="{{ route('admin.thematic-areas.index') }}">
                            <div>
                                <h5><i class="fas fa-tags"></i> Áreas Temáticas</h5>
                                <p class="mb-0 text-muted">Administrar áreas temáticas</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card">
                        <a href="{{ route('admin.profile.edit') }}">
                            <div>
                                <h5><i class="fas fa-user-cog"></i> Mi Perfil</h5>
                                <p class="mb-0 text-muted">Editar información personal</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card">
                        <a href="{{ route('admin.congresses.index') }}">
                            <div>
                                <h5><i class="fas fa-list"></i> Ver Todos los Congresos</h5>
                                <p class="mb-0 text-muted">Gestionar todos los congresos</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="action-card">
                        <a href="{{ route('admin.transactions.index') }}">
                            <div>
                                <h5><i class="fas fa-dollar-sign"></i> Transacciones</h5>
                                <p class="mb-0 text-muted">Ver y gestionar transacciones</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficas -->
        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-header bg-primary">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estado de Congresos</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartEstadoCongresos" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <div class="chart-header bg-success">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Distribución de Estados</h5>
                    </div>
                    <div class="chart-body">
                        <canvas id="chartDistribucion" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Congresos Recientes -->
        <div class="chart-card mt-4">
            <div class="chart-header bg-primary">
                <h5 class="mb-0"><i class="fas fa-list"></i> Congresos Recientes</h5>
            </div>
            <div class="chart-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Fechas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCongresses as $congress)
                                <tr>
                                    <td>
                                        <strong>{{ $congress->title }}</strong><br>
                                        <small class="text-muted">
                                            @foreach($congress->thematicAreas as $area)
                                                <span class="badge bg-primary">{{ $area->name }}</span>
                                            @endforeach
                                        </small>
                                    </td>
                                    <td>
                                        @if($congress->status === 'published')
                                            <span class="badge bg-success">Publicado</span>
                                        @elseif($congress->status === 'draft')
                                            <span class="badge bg-warning">Borrador</span>
                                        @else
                                            <span class="badge bg-secondary">Finalizado</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($congress->start_date)->format('d/m/Y') }} - 
                                        {{ \Carbon\Carbon::parse($congress->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.congresses.show', $congress) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No hay congresos registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Gráfica de estado de congresos
    const ctxEstado = document.getElementById('chartEstadoCongresos');
    if (ctxEstado) {
        new Chart(ctxEstado, {
            type: 'bar',
            data: {
                labels: ['Publicados', 'Borradores', 'Finalizados'],
                datasets: [{
                    label: 'Congresos',
                    data: [
                        {{ $stats['published_congresses'] }},
                        {{ $stats['draft_congresses'] }},
                        {{ $stats['finished_congresses'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Gráfica de distribución
    const ctxDistribucion = document.getElementById('chartDistribucion');
    if (ctxDistribucion) {
        new Chart(ctxDistribucion, {
            type: 'doughnut',
            data: {
                labels: ['Publicados', 'Borradores', 'Finalizados'],
                datasets: [{
                    data: [
                        {{ $stats['published_congresses'] }},
                        {{ $stats['draft_congresses'] }},
                        {{ $stats['finished_congresses'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endpush
