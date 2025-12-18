@extends('layouts.public')

@section('title', 'EventHub | Noticias y Actualidad')

@section('content')
<section class="hero" style="padding: 4rem 0; background: var(--primary-color); color: var(--white);">
    <div class="container reveal">
        <h1 style="color: var(--white); margin-bottom: 1rem;">Noticias y Actualidad</h1>
        <p style="color: #cbd5e1; max-width: 600px; margin: 0 auto;">Mantente informado con las últimas novedades del mundo académico y científico.</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="news-layout">
            <!-- Main Content -->
            <div class="news-main">
                <!-- Featured Article -->
                <article class="featured-article reveal">
                    <img src="{{ asset('img/noticias/destacada.jpg') }}" alt="Conferencia Magistral" class="featured-img" onerror="this.src='https://placehold.co/800x400?text=Noticia+Destacada'">
                    <div class="featured-content">
                        <span class="featured-badge">Destacado</span>
                        <h2 class="featured-title">Innovación Educativa: El Futuro de la Enseñanza Digital en 2026</h2>
                        <p class="mb-4" style="color: #64748b;">Descubre las nuevas metodologías que están transformando las aulas universitarias en todo el mundo. Un análisis profundo sobre la integración de IA y realidad aumentada.</p>
                        <a href="#" class="btn btn-primary">Leer Artículo Completo</a>
                    </div>
                </article>

                <!-- Recent News Grid -->
                <h3 class="mb-8 reveal">Últimas Noticias</h3>
                <div class="news-grid">
                    <!-- News Item 1 -->
                    <article class="news-card reveal reveal-delay-1">
                        <img src="{{ asset('img/noticias/n1.jpg') }}" alt="IA en Educación" class="news-img" onerror="this.src='https://placehold.co/400x250?text=Noticia+1'">
                        <div class="news-content">
                            <span class="news-date"><i class="far fa-calendar-alt"></i> 15 Nov, 2025</span>
                            <h4 class="news-title">Impacto de la Inteligencia Artificial en la Investigación</h4>
                            <a href="#" style="color: var(--accent-color); font-weight: 600;">Leer más &rarr;</a>
                        </div>
                    </article>

                    <!-- News Item 2 -->
                    <article class="news-card reveal reveal-delay-2">
                        <img src="{{ asset('img/noticias/n2.jpg') }}" alt="Becas 2025" class="news-img" onerror="this.src='https://placehold.co/400x250?text=Noticia+2'">
                        <div class="news-content">
                            <span class="news-date"><i class="far fa-calendar-alt"></i> 12 Nov, 2025</span>
                            <h4 class="news-title">Nuevas Becas para Doctorados en Europa</h4>
                            <a href="#" style="color: var(--accent-color); font-weight: 600;">Leer más &rarr;</a>
                        </div>
                    </article>

                    <!-- News Item 3 -->
                    <article class="news-card reveal reveal-delay-3">
                        <img src="{{ asset('img/noticias/n3.jpg') }}" alt="Sostenibilidad" class="news-img" onerror="this.src='https://placehold.co/400x250?text=Noticia+3'">
                        <div class="news-content">
                            <span class="news-date"><i class="far fa-calendar-alt"></i> 08 Nov, 2025</span>
                            <h4 class="news-title">Congreso de Sostenibilidad: Conclusiones Clave</h4>
                            <a href="#" style="color: var(--accent-color); font-weight: 600;">Leer más &rarr;</a>
                        </div>
                    </article>

                    <!-- News Item 4 -->
                    <article class="news-card reveal reveal-delay-1">
                        <img src="{{ asset('img/noticias/img01.jpg') }}" alt="Medicina" class="news-img" onerror="this.src='https://placehold.co/400x250?text=Noticia+4'">
                        <div class="news-content">
                            <span class="news-date"><i class="far fa-calendar-alt"></i> 05 Nov, 2025</span>
                            <h4 class="news-title">Avances en Telemedicina Rural</h4>
                            <a href="#" style="color: var(--accent-color); font-weight: 600;">Leer más &rarr;</a>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar reveal reveal-delay-2">
                <!-- Categories Widget -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Categorías</h4>
                    <ul class="category-list">
                        <li><a href="#">Académico <span>(12)</span></a></li>
                        <li><a href="#">Tecnología <span>(8)</span></a></li>
                        <li><a href="#">Eventos <span>(5)</span></a></li>
                        <li><a href="#">Becas <span>(3)</span></a></li>
                        <li><a href="#">Investigación <span>(7)</span></a></li>
                    </ul>
                </div>

                <!-- Tags Widget -->
                <div class="sidebar-widget">
                    <h4 class="widget-title">Etiquetas Populares</h4>
                    <div class="tag-cloud">
                        <a href="#" class="tag">Innovación</a>
                        <a href="#" class="tag">Ciencia</a>
                        <a href="#" class="tag">Educación</a>
                        <a href="#" class="tag">Digital</a>
                        <a href="#" class="tag">Futuro</a>
                        <a href="#" class="tag">Medicina</a>
                        <a href="#" class="tag">Ingeniería</a>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="sidebar-widget" style="background: var(--primary-color); color: white; border: none;">
                    <h4 class="widget-title" style="border-color: rgba(255,255,255,0.2);">Newsletter</h4>
                    <p style="font-size: 0.9rem; margin-bottom: 1rem; color: #cbd5e1;">Suscríbete para recibir las últimas noticias en tu correo.</p>
                    <form action="{{ route('newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="Tu correo" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: none; margin-bottom: 0.5rem;" required>
                        <button type="submit" class="btn" style="width: 100%; background: var(--secondary-color); color: white; border: none;">Suscribirse</button>
                    </form>
                </div>
            </aside>
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
