<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EventHub | Congresos y Seminarios')</title>
    
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/logo.svg') }}">
    @stack('styles')
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}">EventHub</a>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Inicio</a></li>
                    <li><a href="{{ route('eventos.index') }}" class="{{ request()->routeIs('eventos.*') ? 'active' : '' }}">Congresos</a></li>
                    <li><a href="{{ route('ponentes.index') }}" class="{{ request()->routeIs('ponentes.*') ? 'active' : '' }}">Ponentes</a></li>
                    <li><a href="{{ route('patrocinadores.index') }}" class="{{ request()->routeIs('patrocinadores.*') ? 'active' : '' }}">Patrocinadores</a></li>
                    <li><a href="{{ route('noticias.index') }}" class="{{ request()->routeIs('noticias.*') ? 'active' : '' }}">Noticias</a></li>
                    <li><a href="{{ route('contacto.index') }}" class="{{ request()->routeIs('contacto.*') ? 'active' : '' }}">Contacto</a></li>
                    
                    <li class="nav-divider">|</li>

                    @auth
                        @if(Auth::user()->hasRole(['Super Admin', 'Admin']))
                            <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}">Mi Cuenta</a></li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="color: #dc2626;">Salir</a>
                            </form>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="btn-login">Iniciar Sesión</a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="btn-register">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <!-- Contenido principal -->
    <main>
        @yield('content')
    </main>

    <!-- Pie de página -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h4>EventHub</h4>
                    <p style="margin-top: 1rem; font-size: 0.9rem;">Plataforma líder en gestión de congresos y eventos académicos internacionales.</p>
                </div>
                <div>
                    <h4>Enlaces</h4>
                    <ul>
                        <li><a href="{{ route('eventos.index') }}">Congresos</a></li>
                        <li><a href="{{ route('ponentes.index') }}">Ponentes</a></li>
                        <li><a href="{{ route('patrocinadores.index') }}">Patrocinadores</a></li>
                        <li><a href="{{ route('noticias.index') }}">Noticias</a></li>
                        <li><a href="{{ route('contacto.index') }}">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Política de Privacidad</a></li>
                        <li><a href="#">Términos y Condiciones</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 EventHub. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
