@extends('layouts.public')

@section('title', 'EventHub | Nuestros Congresos')

@section('content')
<section class="hero" style="padding: 4rem 0; background: var(--primary-color); color: var(--white);">
    <div class="container reveal">
        <h1 style="color: var(--white); margin-bottom: 1rem;">Nuestros Congresos</h1>
        <p style="color: #cbd5e1; max-width: 600px; margin: 0 auto;">Explora nuestra agenda académica y participa en eventos de talla mundial.</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="text-center mb-8 reveal">
            <h2>Calendario de Eventos 2025-2026</h2>
            <p>Inscripciones abiertas para las siguientes fechas</p>
        </div>

        <div class="event-list">
            <!-- Evento 1 -->
            <div class="event-item reveal reveal-delay-1">
                <div class="event-date-box">
                    <span class="day">10</span>
                    <span class="month">Nov 2025</span>
                </div>
                <div class="event-info">
                    <h3>Congreso de Innovación Tecnológica</h3>
                    <p>Lima, Perú — Presencial y Virtual</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Temas: IA, Blockchain, Ciberseguridad</p>
                </div>
                <div class="event-action">
                    <a href="#" class="btn btn-primary">Inscribirse</a>
                </div>
            </div>

            <!-- Evento 2 -->
            <div class="event-item reveal reveal-delay-2">
                <div class="event-date-box">
                    <span class="day">25</span>
                    <span class="month">Nov 2025</span>
                </div>
                <div class="event-info">
                    <h3>Seminario Digital de Marketing</h3>
                    <p>Virtual — Plataforma Zoom</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Temas: SEO, Growth Hacking, Redes Sociales</p>
                </div>
                <div class="event-action">
                    <a href="#" class="btn btn-outline">Ver Detalles</a>
                </div>
            </div>

            <!-- Evento 3 -->
            <div class="event-item reveal reveal-delay-3">
                <div class="event-date-box">
                    <span class="day">02</span>
                    <span class="month">Dic 2025</span>
                </div>
                <div class="event-info">
                    <h3>Congreso Internacional de Ciencia</h3>
                    <p>Arequipa, Perú</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Temas: Biotecnología, Energías Renovables</p>
                </div>
                <div class="event-action">
                    <a href="#" class="btn btn-outline">Call for Papers</a>
                </div>
            </div>

            <!-- Evento 4 -->
            <div class="event-item reveal reveal-delay-1">
                <div class="event-date-box">
                    <span class="day">15</span>
                    <span class="month">Ene 2026</span>
                </div>
                <div class="event-info">
                    <h3>Simposio de Arquitectura Sostenible</h3>
                    <p>Cusco, Perú</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Temas: Urbanismo, Materiales Ecológicos</p>
                </div>
                <div class="event-action">
                    <span class="event-status">Próximamente</span>
                </div>
            </div>

            <!-- Evento 5 -->
            <div class="event-item reveal reveal-delay-2">
                <div class="event-date-box">
                    <span class="day">20</span>
                    <span class="month">Feb 2026</span>
                </div>
                <div class="event-info">
                    <h3>Cumbre de Liderazgo Empresarial</h3>
                    <p>Bogotá, Colombia (Híbrido)</p>
                    <p style="font-size: 0.9rem; margin-top: 0.5rem;">Temas: Gestión de Equipos, Estrategia</p>
                </div>
                <div class="event-action">
                    <span class="event-status">Próximamente</span>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    const reveals = document.querySelectorAll('.reveal');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal-active');
            }
        });
    }, { threshold: 0.1 });
    
    reveals.forEach(reveal => revealObserver.observe(reveal));
</script>
@endpush
