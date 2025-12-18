@extends('layouts.public')

@section('title', 'EventHub | Ponentes')

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <h1>Ponentes Destacados</h1>
        <p>Conoce a los expertos que compartirán sus conocimientos.</p>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="ponentes-grid">
            <div class="ponente-card reveal reveal-delay-1">
                <img src="{{ asset('img/ponentes/ponente1.jpg') }}" alt="Ponente 1" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+1'">
                <div class="ponente-info">
                    <h3>Dr. Carlos Mendoza</h3>
                    <p class="cargo">Director de Investigación</p>
                    <p class="tema">Tema: "Inteligencia Artificial Aplicada"</p>
                </div>
            </div>

            <div class="ponente-card reveal reveal-delay-2">
                <img src="{{ asset('img/ponentes/ponente2.jpg') }}" alt="Ponente 2" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+2'">
                <div class="ponente-info">
                    <h3>Lic. Ana Torres</h3>
                    <p class="cargo">Especialista en Finanzas Corporativas</p>
                    <p class="tema">Tema: "Innovación Financiera"</p>
                </div>
            </div>

            <div class="ponente-card reveal reveal-delay-3">
                <img src="{{ asset('img/ponentes/ponente3.jpg') }}" alt="Ponente 3" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+3'">
                <div class="ponente-info">
                    <h3>Ing. Roberto López</h3>
                    <p class="cargo">Director de Innovación Tecnológica</p>
                    <p class="tema">Tema: "Transformación Digital"</p>
                </div>
            </div>

            <div class="ponente-card reveal reveal-delay-1">
                <img src="{{ asset('img/ponentes/ponente4.jpg') }}" alt="Ponente 4" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+4'">
                <div class="ponente-info">
                    <h3>Dra. Lucía Fernández</h3>
                    <p class="cargo">Profesora Investigadora</p>
                    <p class="tema">Tema: "Educación y Tecnología"</p>
                </div>
            </div>

            <div class="ponente-card reveal reveal-delay-2">
                <img src="{{ asset('img/ponentes/ponente5.jpg') }}" alt="Ponente 5" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+5'">
                <div class="ponente-info">
                    <h3>Dr. Javier Ramos</h3>
                    <p class="cargo">Experto en Ciberseguridad</p>
                    <p class="tema">Tema: "Protección de Datos e IA"</p>
                </div>
            </div>

            <div class="ponente-card reveal reveal-delay-3">
                <img src="{{ asset('img/ponentes/ponente6.jpg') }}" alt="Ponente 6" class="ponente-image" onerror="this.src='https://placehold.co/300x400?text=Ponente+6'">
                <div class="ponente-info">
                    <h3>Lic. Mariana Silva</h3>
                    <p class="cargo">Consultora en Innovación</p>
                    <p class="tema">Tema: "Creatividad y Liderazgo"</p>
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
