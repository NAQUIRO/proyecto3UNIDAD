@extends('layouts.public')

@section('title', 'EventHub | Portal de Congresos y Seminarios')

@section('content')
    <!-- Banner principal -->
    <section class="banner">
        <div class="banner-text">
            <h2>Explora los mejores Congresos y Seminarios del año</h2>
            <p>Conecta con expertos, comparte conocimiento y vive una experiencia académica única.</p>
        </div>
    </section>

    <!-- Sección de próximos eventos -->
    <section class="eventos">
        <div class="container">
            <h2>Próximos eventos</h2>
            <div class="cards">
                @php
                    $eventos = \App\Models\Evento::where('destacado', true)
                        ->orderBy('fecha_inicio', 'asc')
                        ->limit(3)
                        ->get();
                @endphp

                @forelse($eventos as $evento)
                    <div class="card">
                        <img src="{{ asset($evento->imagen) }}" alt="{{ $evento->titulo }}">
                        <h3>{{ $evento->titulo }}</h3>
                        <p>{{ $evento->fecha_texto ?? $evento->fecha_inicio->format('d/m/Y') }}</p>
                    </div>
                @empty
                    <div class="card">
                        <img src="{{ asset('img/eventos/evento1.jpg') }}" alt="Congreso de Innovación Tecnológica">
                        <h3>Congreso de Innovación Tecnológica</h3>
                        <p>Del 10 al 12 de noviembre — Lima, Perú</p>
                    </div>
                    <div class="card">
                        <img src="{{ asset('img/eventos/evento2.jpg') }}" alt="Seminario de Transformación Digital">
                        <h3>Seminario de Transformación Digital</h3>
                        <p>25 de noviembre — Virtual</p>
                    </div>
                    <div class="card">
                        <img src="{{ asset('img/eventos/evento3.jpg') }}" alt="Congreso Internacional de Ciencia y Educación">
                        <h3>Congreso Internacional de Ciencia y Educación</h3>
                        <p>2 al 4 de diciembre — Arequipa, Perú</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Sección: Por qué participar -->
    <section class="beneficios">
        <div class="container">
            <h2>¿Por qué participar?</h2>
            <div class="beneficios-lista">
                <div class="beneficio">
                    <h3>Aprendizaje</h3>
                    <p>Accede a charlas de expertos y amplía tus conocimientos en diversas áreas.</p>
                </div>
                <div class="beneficio">
                    <h3>Networking</h3>
                    <p>Conecta con profesionales, investigadores y estudiantes de todo el país.</p>
                </div>
                <div class="beneficio">
                    <h3>Experiencia</h3>
                    <p>Vive una experiencia académica enriquecedora con talleres y exposiciones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Nueva sección: Sobre EventHub -->
    <section class="sobre">
        <div class="container">
            <h2>Sobre EventHub</h2>
            <p>
                <strong>EventHub</strong> es una plataforma dedicada a la difusión y promoción de congresos, seminarios y eventos académicos
                a nivel nacional e internacional. Nuestro objetivo es conectar a estudiantes, profesionales e instituciones
                en un espacio digital donde el conocimiento y la innovación se encuentren.
            </p>
            <p>
                Aquí encontrarás información actualizada sobre los próximos eventos, ponentes destacados y oportunidades
                para fortalecer tu desarrollo académico y profesional.
            </p>
        </div>
    </section>
@endsection
