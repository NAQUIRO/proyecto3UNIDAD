@extends('layouts.public')

@section('title', 'EventHub | Eventos Destacados')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
    <!-- Banner principal -->
    <section class="hero hero-eventos">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Eventos Destacados</h1>
            <p>Descubre los congresos y seminarios más importantes del año</p>
        </div>
    </section>

    <!-- Lista de eventos -->
    <main class="container" style="padding: 60px 0;">
        <div class="grid-3">
            @php
                $eventos = \App\Models\Evento::orderBy('fecha_inicio', 'asc')->get();
            @endphp

            @forelse($eventos as $evento)
                <article class="card">
                    <img src="{{ asset($evento->imagen) }}" alt="{{ $evento->titulo }}" class="event-img">
                    <h2 class="event-title">{{ $evento->titulo }}</h2>
                    <p class="event-date">{{ $evento->fecha_texto ?? $evento->fecha_inicio->format('d/m/Y') }}</p>
                    <p>{{ $evento->descripcion_breve }}</p>
                    <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
                    <div class="event-details">
                        @if($evento->lugar)
                            <p><strong>Lugar:</strong> {{ $evento->lugar }}</p>
                        @endif
                        @if($evento->horario)
                            <p><strong>Horario:</strong> {{ $evento->horario }}</p>
                        @endif
                        @if($evento->precio)
                            <p><strong>Precio:</strong> €{{ number_format($evento->precio, 2) }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <!-- Eventos de ejemplo si no hay en BD -->
                <article class="card">
                    <img src="{{ asset('img/eventos/tecnologia.jpg') }}" alt="Congreso de Tecnología Digital" class="event-img">
                    <h2 class="event-title">Congreso de Tecnología Digital</h2>
                    <p class="event-date">15-17 de marzo, 2025</p>
                    <p>Un evento imperdible sobre inteligencia artificial, blockchain y desarrollo web.</p>
                    <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
                    <div class="event-details">
                        <p><strong>Lugar:</strong> Centro de Convenciones Madrid</p>
                        <p><strong>Horario:</strong> 9:00 - 18:00</p>
                        <p><strong>Precio:</strong> €299</p>
                        <p><strong>Ponentes:</strong> 25+ expertos internacionales</p>
                    </div>
                </article>

                <article class="card">
                    <img src="{{ asset('img/eventos/marketing.jpg') }}" alt="Seminario de Marketing Digital" class="event-img">
                    <h2 class="event-title">Seminario de Marketing Digital</h2>
                    <p class="event-date">22 de abril, 2025</p>
                    <p>Aprende estrategias reales y efectivas para el crecimiento digital.</p>
                    <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
                    <div class="event-details">
                        <p><strong>Lugar:</strong> Hotel Business Center Barcelona</p>
                        <p><strong>Horario:</strong> 10:00 - 17:00</p>
                        <p><strong>Precio:</strong> €149</p>
                        <p><strong>Ponentes:</strong> Especialistas en marketing digital</p>
                    </div>
                </article>

                <article class="card">
                    <img src="{{ asset('img/eventos/salud.jpg') }}" alt="Congreso Internacional de Salud" class="event-img">
                    <h2 class="event-title">Congreso Internacional de Salud</h2>
                    <p class="event-date">5-7 de mayo, 2025</p>
                    <p>El evento más importante del año en medicina y telemedicina.</p>
                    <button class="btn toggle-btn" onclick="toggleDetails(this)">Ver detalles ▼</button>
                    <div class="event-details">
                        <p><strong>Lugar:</strong> Palacio de Congresos Valencia</p>
                        <p><strong>Horario:</strong> 8:30 - 19:00</p>
                        <p><strong>Precio:</strong> €450 / €250 (estudiantes)</p>
                        <p><strong>Ponentes:</strong> 40+ médicos y expertos</p>
                    </div>
                </article>
            @endforelse
        </div>
    </main>
@endsection

@push('scripts')
<script>
    function toggleDetails(button) {
        const details = button.nextElementSibling;
        const isOpen = details.style.display === 'block';
        details.style.display = isOpen ? 'none' : 'block';
        button.textContent = isOpen ? 'Ver detalles ▼' : 'Ocultar detalles ▲';
    }
</script>
@endpush

