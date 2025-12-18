@extends('layouts.public')

@section('title', 'EventHub | Congresos y Seminarios')

@section('content')
<section class="hero">
    <div class="container reveal">
        <p class="reveal-delay-1">— IMPULSE SU CURRICULUM ACADÉMICO —</p>
        <h1 class="reveal-delay-2">Congresos internacionales científicos y seminarios de vanguardia</h1>
        <p class="reveal-delay-3">Conecta con expertos, comparte conocimiento y vive una experiencia académica única.</p>
        <a href="{{ route('eventos.index') }}" class="btn btn-primary reveal-delay-3">Ver Congresos Programados</a>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="text-center mb-8 reveal">
            <h2>Próximos Eventos</h2>
            <p>Organizados junto a instituciones académicas de referencia</p>
        </div>

        <div class="event-list">
            <!-- Evento 1 -->
            <div class="event-item reveal">
                <div class="event-date-box">
                    <span class="day">10</span>
                    <span class="month">Nov 2025</span>
                </div>
                <div class="event-info">
                    <h3>Congreso de Innovación Tecnológica</h3>
                    <p>Lima, Perú — Presencial y Virtual</p>
                </div>
                <div class="event-action">
                    <span class="event-status">Abierto Registro &rarr;</span>
                </div>
            </div>

            <!-- Evento 2 -->
            <div class="event-item reveal">
                <div class="event-date-box">
                    <span class="day">25</span>
                    <span class="month">Nov 2025</span>
                </div>
                <div class="event-info">
                    <h3>Seminario Digital de Marketing</h3>
                    <p>Virtual — Plataforma Zoom</p>
                </div>
                <div class="event-action">
                    <span class="event-status">En Preparación &rarr;</span>
                </div>
            </div>

            <!-- Evento 3 -->
            <div class="event-item reveal">
                <div class="event-date-box">
                    <span class="day">02</span>
                    <span class="month">Dic 2025</span>
                </div>
                <div class="event-info">
                    <h3>Congreso Internacional de Ciencia</h3>
                    <p>Arequipa, Perú</p>
                </div>
                <div class="event-action">
                    <span class="event-status">Call for Papers &rarr;</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background-color: #f8fafc;">
    <div class="container">
        <div class="text-center mb-8 reveal">
            <h2>Áreas de Conocimiento</h2>
            <p>Consulte nuestra oferta de Congresos según su interés</p>
        </div>
        <div class="areas-grid">
            <a href="#" class="area-card reveal reveal-delay-1">
                <h4>Ciencias de la Salud</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-2">
                <h4>Educación</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-3">
                <h4>Tecnología</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-1">
                <h4>Ciencias Sociales</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-2">
                <h4>Arte y Humanidades</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-3">
                <h4>Economía</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-1">
                <h4>Derecho</h4>
            </a>
            <a href="#" class="area-card reveal reveal-delay-2">
                <h4>Ingeniería</h4>
            </a>
        </div>
    </div>
</section>

<section class="newsletter reveal">
    <div class="container">
        <h2>Newsletter</h2>
        <p>Suscríbase y manténgase al día de los nuevos congresos confirmados</p>
        <form class="newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Su correo electrónico" class="newsletter-input" required>
            <button type="submit" class="btn-subscribe">Suscribirme</button>
        </form>
    </div>
</section>

<section class="section-padding">
    <div class="container text-center">
        <h2 class="reveal">Nuestra experiencia a su servicio</h2>
        <p class="mb-4 reveal">Más de 20.000 participantes han pasado por nuestros congresos.</p>
        <div class="grid-3">
            <div class="reveal reveal-delay-1">
                <h3 style="font-size: 2.5rem; color: var(--secondary-color);">+<span class="counter-number" data-target="50">0</span></h3>
                <p>Eventos Realizados</p>
            </div>
            <div class="reveal reveal-delay-2">
                <h3 style="font-size: 2.5rem; color: var(--secondary-color);">+<span class="counter-number" data-target="20" data-suffix="k">0</span></h3>
                <p>Participantes</p>
            </div>
            <div class="reveal reveal-delay-3">
                <h3 style="font-size: 2.5rem; color: var(--secondary-color);"><span class="counter-number" data-target="100" data-suffix="%">0</span></h3>
                <p>Satisfacción</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-number');
        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-target'));
            const suffix = counter.getAttribute('data-suffix') || '';
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = Math.floor(current) + suffix;
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target + suffix;
                }
            };
            
            updateCounter();
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        counters.forEach(counter => observer.observe(counter));
    });
    
    // Reveal animation
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
