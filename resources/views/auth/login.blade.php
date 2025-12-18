<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - EventHub</title>

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body style="margin: 0; padding: 0; font-family: var(--font-body);">
    <div style="display: flex; min-height: 100vh; width: 100%;">

        <!-- Left Side - Form (White Background) -->
        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #ffffff; padding: 2rem;">
            <div style="width: 100%; max-width: 400px;">

                <!-- Logo/Brand -->
                <a href="{{ route('home') }}" style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--primary-color); text-decoration: none; display: block; margin-bottom: 3rem;">
                    EventHub
                </a>

                <!-- Title -->
                <h1 style="font-family: var(--font-heading); font-size: 2rem; font-weight: 400; color: #333; margin-bottom: 2.5rem;">
                    Welcome back,
                </h1>

                <!-- Alerts -->
                @if (session('status'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul style="margin: 0; padding-left: 1.5rem; list-style: none;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'">
                    </div>

                    <!-- Password Input -->
                    <div style="margin-bottom: 1rem; position: relative;">
                        <label style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #999; margin-bottom: 0.5rem;">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            style="width: 100%; padding: 0.75rem 0; border: none; border-bottom: 1px solid #ddd; font-size: 1rem; outline: none; transition: border-color 0.3s; background: transparent;"
                            onfocus="this.style.borderBottomColor='var(--secondary-color)'"
                            onblur="this.style.borderBottomColor='#ddd'">
                        <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 0; bottom: 0.75rem; color: #999; cursor: pointer; font-size: 0.9rem;"></i>
                    </div>

                    <!-- Remember Me -->
                    <div style="margin-bottom: 1rem;">
                        <label style="display: flex; align-items: center; font-size: 0.85rem; color: #666; cursor: pointer;">
                            <input type="checkbox" name="remember" id="remember_me" style="margin-right: 0.5rem;">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <!-- Forgot Password -->
                    <div style="text-align: right; margin-bottom: 2rem;">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: #999; text-decoration: none;">Forgot password?</a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" style="width: 100%; padding: 1rem; background: #000; color: white; border: none; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; cursor: pointer; transition: all 0.3s; margin-bottom: 1.5rem;"
                        onmouseover="this.style.background='#333'" onmouseout="this.style.background='#000'">
                        SIGN IN
                    </button>

                    <!-- Social Login -->
                    <button type="button" onclick="alert('Facebook login no disponible')" style="width: 100%; padding: 1rem; background: white; color: #666; border: 1px solid #ddd; border-radius: 50px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s;">
                        Connect with <strong>facebook</strong>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side - Image with CTA -->
        <div style="flex: 1; position: relative; background: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.85)), url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 3rem;">

            <h2 style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 400; margin-bottom: 1rem; text-align: center;">
                New here?
            </h2>
            <p style="font-size: 1rem; margin-bottom: 2.5rem; text-align: center; max-width: 400px; opacity: 0.95;">
                Sign up and discover great amount of new opportunities!
            </p>

            <a href="{{ route('register') }}" style="padding: 0.875rem 3rem; background: transparent; color: white; border: 2px solid white; border-radius: 50px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; text-decoration: none; transition: all 0.3s;"
                onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                SIGN UP
            </a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>
