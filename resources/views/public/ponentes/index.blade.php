@extends('layouts.public')

@section('title', 'Ponentes - ' . config('app.name'))

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <h1>Ponentes Destacados</h1>
        <p>Conoce a los expertos que compartirán sus conocimientos.</p>
    </div>
</div>

<div class="container grid-3" style="padding-bottom: 4rem;">
    @forelse($speakers as $speaker)
        <div class="tarjeta-ponente reveal {{ $loop->iteration % 3 == 1 ? 'reveal-delay-1' : ($loop->iteration % 3 == 2 ? 'reveal-delay-2' : 'reveal-delay-3') }}">
            <img 
                src="{{ $speaker->photo_url ?? asset('img/ponentes/ponente' . (($loop->index % 6) + 1) . '.jpg') }}" 
                alt="{{ $speaker->name }}"
                onerror="this.src='{{ asset('img/default-speaker.jpg') }}'"
            >
            <div class="card-content">
                <h3>{{ $speaker->name }}</h3>
                @if($speaker->position)
                    <p class="cargo">{{ $speaker->position }}</p>
                @endif
                @if($speaker->specialization)
                    <p class="tema">Tema: "{{ $speaker->specialization }}"</p>
                @elseif($speaker->institution)
                    <p class="tema">{{ $speaker->institution }}</p>
                @endif
            </div>
        </div>
    @empty
        <!-- Ponentes de ejemplo si no hay datos -->
        <div class="tarjeta-ponente reveal reveal-delay-1">
            <img src="{{ asset('img/ponentes/ponente2.jpg') }}" alt="Ponente 2" onerror="this.src='{{ asset('img/default-speaker.jpg') }}'">
            <div class="card-content">
                <h3>Lic. Ana Torres</h3>
                <p class="cargo">Especialista en Finanzas Corporativas</p>
                <p class="tema">Tema: "Innovación Financiera"</p>
            </div>
        </div>

        <div class="tarjeta-ponente reveal reveal-delay-2">
            <img src="{{ asset('img/ponentes/ponente3.jpg') }}" alt="Ponente 3" onerror="this.src='{{ asset('img/default-speaker.jpg') }}'">
            <div class="card-content">
                <h3>Ing. Roberto López</h3>
                <p class="cargo">Director de Innovación Tecnológica</p>
                <p class="tema">Tema: "Transformación Digital"</p>
            </div>
        </div>

        <div class="tarjeta-ponente reveal reveal-delay-3">
            <img src="{{ asset('img/ponentes/ponente4.jpg') }}" alt="Ponente 4" onerror="this.src='{{ asset('img/default-speaker.jpg') }}'">
            <div class="card-content">
                <h3>Dra. Lucía Fernández</h3>
                <p class="cargo">Profesora Investigadora</p>
                <p class="tema">Tema: "Educación y Tecnología"</p>
            </div>
        </div>

        <div class="tarjeta-ponente reveal reveal-delay-1">
            <img src="{{ asset('img/ponentes/ponente5.jpg') }}" alt="Ponente 5" onerror="this.src='{{ asset('img/default-speaker.jpg') }}'">
            <div class="card-content">
                <h3>Dr. Javier Ramos</h3>
                <p class="cargo">Experto en Ciberseguridad</p>
                <p class="tema">Tema: "Protección de Datos e IA"</p>
            </div>
        </div>

        <div class="tarjeta-ponente reveal reveal-delay-2">
            <img src="{{ asset('img/ponentes/ponente6.jpg') }}" alt="Ponente 6" onerror="this.src='{{ asset('img/default-speaker.jpg') }}'">
            <div class="card-content">
                <h3>Lic. Mariana Silva</h3>
                <p class="cargo">Consultora en Innovación</p>
                <p class="tema">Tema: "Creatividad y Liderazgo"</p>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('styles')
<style>
    .tarjeta-ponente {
        background: var(--white);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--border-color);
    }

    .tarjeta-ponente:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    .tarjeta-ponente img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        display: block;
    }

    .tarjeta-ponente .card-content {
        padding: 1.5rem;
    }

    .tarjeta-ponente h3 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        color: var(--primary-color);
        font-family: var(--font-heading);
    }

    .tarjeta-ponente .cargo {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .tarjeta-ponente .tema {
        color: #64748b;
        font-size: 0.9rem;
        margin: 0;
    }

    .reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
    }

    .reveal-active {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-delay-1 {
        transition-delay: 0.1s;
    }

    .reveal-delay-2 {
        transition-delay: 0.2s;
    }

    .reveal-delay-3 {
        transition-delay: 0.3s;
    }

    @media (max-width: 768px) {
        .grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

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
