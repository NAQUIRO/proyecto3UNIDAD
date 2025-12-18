@extends('layouts.public')

@section('title', 'EventHub | Contacto')

@section('content')
<section class="hero" style="padding: 4rem 0; background: var(--primary-color); color: var(--white);">
    <div class="container reveal">
        <h1 style="color: var(--white); margin-bottom: 1rem;">Contáctanos</h1>
        <p style="color: #cbd5e1; max-width: 600px; margin: 0 auto;">Estamos aquí para ayudarte. Envíanos tus dudas o sugerencias y te responderemos a la brevedad.</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="contact-wrapper">
            <!-- Form Column -->
            <div class="contact-form-container reveal reveal-delay-1">
                <h2 class="mb-4">Envíanos un mensaje</h2>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contacto.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="asunto">Asunto</label>
                        <input type="text" id="asunto" name="asunto" placeholder="Motivo de tu consulta" value="{{ old('asunto') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea id="mensaje" name="mensaje" rows="5" placeholder="¿En qué podemos ayudarte?" required>{{ old('mensaje') }}</textarea>
                    </div>
                    <button type="submit" class="btn-submit">Enviar Mensaje</button>
                </form>
            </div>

            <!-- Info Column -->
            <div class="contact-info-container reveal reveal-delay-2">
                <h2>Información de Contacto</h2>
                <p class="mb-8" style="color: #64748b;">Visítanos en nuestras oficinas o contáctanos por nuestros canales oficiales.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-text">
                            <h4>Ubicación</h4>
                            <p>Av. Principal 123, San Isidro<br>Lima, Perú</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-text">
                            <h4>Email</h4>
                            <p>contacto@eventhub.com<br>soporte@eventhub.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone"></i></div>
                        <div class="contact-text">
                            <h4>Teléfono</h4>
                            <p>+51 (01) 123-4567<br>+51 999 888 777</p>
                        </div>
                    </div>
                </div>

                <div class="map-container">
                    <iframe src="https://maps.google.com/maps?q=San%20Isidro%2C%20Lima&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
