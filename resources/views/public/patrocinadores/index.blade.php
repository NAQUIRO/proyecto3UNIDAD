@extends('layouts.public')

@section('title', 'EventHub | Patrocinadores')

@section('content')
<div class="titulo-seccion">
    <div class="container">
        <h1>Nuestros Patrocinadores</h1>
        <p>Empresas e instituciones que hacen posible la realización de eventos de excelencia</p>
    </div>
</div>

<section class="patrocinadores-section">
    <div class="container">
        <h2 class="category-title" style="text-align: center; margin-bottom: 2rem;">Patrocinadores Principales</h2>
        
        <div class="patrocinadores-grid principal">
            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/microsoft.png') }}" alt="Microsoft">
                </div>
                <h3>Microsoft</h3>
                <p class="sector">Tecnología e Innovación</p>
                <p class="descripcion">Líder global en software, servicios en la nube y soluciones empresariales.</p>
            </div>

            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/bbva.png') }}" alt="BBVA">
                </div>
                <h3>BBVA</h3>
                <p class="sector">Servicios Financieros</p>
                <p class="descripcion">Banco global comprometido con la innovación financiera y transformación digital.</p>
            </div>

            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/google.png') }}" alt="Google">
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
                    <img src="{{ asset('img/patrocinadores/scotiabank.png') }}" alt="Scotiabank">
                </div>
                <h3>Scotiabank</h3>
                <p class="sector">Banca y Finanzas</p>
            </div>

            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/bcp.png') }}" alt="BCP">
                </div>
                <h3>BCP</h3>
                <p class="sector">Servicios Bancarios</p>
            </div>

            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/facebook.png') }}" alt="Meta">
                </div>
                <h3>Meta</h3>
                <p class="sector">Redes Sociales</p>
            </div>

            <div class="patrocinador-card">
                <div class="logo-container">
                    <img src="{{ asset('img/patrocinadores/aws.png') }}" alt="AWS">
                </div>
                <h3>AWS</h3>
                <p class="sector">Cloud Computing</p>
            </div>
        </div>
    </div>
</section>

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
