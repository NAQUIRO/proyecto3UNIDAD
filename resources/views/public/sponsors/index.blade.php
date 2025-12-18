@extends('layouts.public')

@section('title', 'Patrocinadores - ' . config('app.name'))

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <h1>Nuestros Patrocinadores</h1>
        <p>Empresas e instituciones que hacen posible la realización de eventos de excelencia</p>
    </div>
</div>

@if($sponsorsByType->has('platinum') || $sponsorsByType->has('gold'))
<section class="patrocinadores-section">
    <div class="container">
        <h2 class="category-title" style="text-align: center; margin-bottom: 2rem;">Patrocinadores Principales</h2>
        
        <div class="patrocinadores-grid principal">
            @foreach(['platinum', 'gold'] as $type)
                @if($sponsorsByType->has($type))
                    @foreach($sponsorsByType[$type] as $sponsor)
                        <div class="patrocinador-card">
                            <div class="logo-container">
                                <img 
                                    src="{{ $sponsor->logo_url ?? asset('img/patrocinadores/default.png') }}" 
                                    alt="{{ $sponsor->name }}"
                                    onerror="this.src='{{ asset('img/default-logo.png') }}'"
                                >
                            </div>
                            <h3>{{ $sponsor->name }}</h3>
                            @if($sponsor->description)
                                <p class="sector">{{ Str::limit($sponsor->description, 80) }}</p>
                            @endif
                            @if($sponsor->description && strlen($sponsor->description) > 80)
                                <p class="descripcion">{{ Str::limit($sponsor->description, 120) }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@if($sponsorsByType->has('silver') || $sponsorsByType->has('bronze') || $sponsorsByType->has('partner'))
<section class="patrocinadores-section" style="background-color: #f8fafc;">
    <div class="container">
        <h2 class="category-title" style="text-align: center; margin-bottom: 2rem;">Patrocinadores Oficiales</h2>
        <div class="patrocinadores-grid">
            @foreach(['silver', 'bronze', 'partner'] as $type)
                @if($sponsorsByType->has($type))
                    @foreach($sponsorsByType[$type] as $sponsor)
                        <div class="patrocinador-card">
                            <div class="logo-container">
                                <img 
                                    src="{{ $sponsor->logo_url ?? asset('img/patrocinadores/default.png') }}" 
                                    alt="{{ $sponsor->name }}"
                                    onerror="this.src='{{ asset('img/default-logo.png') }}'"
                                >
                            </div>
                            <h3>{{ $sponsor->name }}</h3>
                            @if($sponsor->description)
                                <p class="sector">{{ Str::limit($sponsor->description, 80) }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

@if($sponsors->count() == 0)
    <!-- Patrocinadores de ejemplo si no hay datos -->
    <section class="patrocinadores-section">
        <div class="container">
            <h2 class="category-title" style="text-align: center; margin-bottom: 2rem;">Patrocinadores Principales</h2>
            
            <div class="patrocinadores-grid principal">
                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/microsoft.png') }}" alt="Microsoft" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>Microsoft</h3>
                    <p class="sector">Tecnología e Innovación</p>
                    <p class="descripcion">Líder global en software, servicios en la nube y soluciones empresariales.</p>
                </div>

                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/bbva.png') }}" alt="BBVA" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>BBVA</h3>
                    <p class="sector">Servicios Financieros</p>
                    <p class="descripcion">Banco global comprometido con la innovación financiera y transformación digital.</p>
                </div>

                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/google.png') }}" alt="Google" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>Google</h3>
                    <p class="sector">Tecnología Digital</p>
                    <p class="descripcion">Innovación en búsqueda, publicidad digital y servicios en la nube.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="patrocinadores-section" style="background-color: #f8fafc;">
        <div class="container">
            <h2 class="category-title" style="text-align: center; margin-bottom: 2rem;">Patrocinadores Oficiales</h2>
            <div class="patrocinadores-grid">
                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/scotiabank.png') }}" alt="Scotiabank" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>Scotiabank</h3>
                    <p class="sector">Banca y Finanzas</p>
                </div>

                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/bcp.png') }}" alt="BCP" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>BCP</h3>
                    <p class="sector">Servicios Bancarios</p>
                </div>

                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/facebook.png') }}" alt="Meta" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>Meta</h3>
                    <p class="sector">Redes Sociales</p>
                </div>

                <div class="patrocinador-card">
                    <div class="logo-container">
                        <img src="{{ asset('img/patrocinadores/aws.png') }}" alt="AWS" onerror="this.src='{{ asset('img/default-logo.png') }}'">
                    </div>
                    <h3>AWS</h3>
                    <p class="sector">Cloud Computing</p>
                </div>
            </div>
        </div>
    </section>
@endif

<section class="cta-patrocinio" style="text-align: center; padding: 4rem 0;">
    <div class="container">
        <div class="cta-content">
            <h2>¿Quieres ser nuestro patrocinador?</h2>
            <p style="margin-bottom: 1.5rem;">Únete a las empresas líderes que apoyan el desarrollo del conocimiento.</p>
            <a href="{{ route('contacto.index') }}" class="btn">Contáctanos</a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .patrocinadores-section {
        padding: 4rem 0;
    }

    .category-title {
        font-size: 2rem;
        font-family: var(--font-heading);
        color: var(--primary-color);
        margin-bottom: 2rem;
    }

    .patrocinadores-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .patrocinadores-grid.principal {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }

    .patrocinador-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 2rem;
        text-align: center;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .patrocinador-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    .logo-container {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--light-bg);
        border-radius: var(--radius);
    }

    .logo-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(20%);
        transition: filter 0.3s ease;
    }

    .patrocinador-card:hover .logo-container img {
        filter: grayscale(0%);
    }

    .patrocinador-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        color: var(--primary-color);
        font-family: var(--font-heading);
    }

    .patrocinador-card .sector {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .patrocinador-card .descripcion {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.6;
        margin: 0;
    }

    .cta-patrocinio {
        background: var(--light-bg);
    }

    .cta-content h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .cta-content p {
        color: #64748b;
        font-size: 1.1rem;
    }

    .cta-patrocinio .btn {
        display: inline-block;
        padding: 1rem 2.5rem;
        background: var(--primary-color);
        color: var(--white);
        border-radius: var(--radius);
        font-weight: 600;
        transition: background 0.3s ease;
        text-decoration: none;
    }

    .cta-patrocinio .btn:hover {
        background: #000;
        color: var(--white);
    }

    @media (max-width: 768px) {
        .patrocinadores-grid {
            grid-template-columns: 1fr;
        }

        .patrocinadores-grid.principal {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
