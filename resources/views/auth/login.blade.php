<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión – Kiosko UNER</title>
    <meta name="description" content="Ingresá al sistema de gestión del Kiosko UNER con tu usuario y contraseña.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login/login.css') }}">
</head>
<body>

{{-- Floating particles --}}
<div class="login-particles">
    <div class="login-particle"></div>
    <div class="login-particle"></div>
    <div class="login-particle"></div>
    <div class="login-particle"></div>
    <div class="login-particle"></div>
    <div class="login-particle"></div>
</div>

{{-- ════════════════════════════════════════════════════════════
     LOGIN CARD
     ════════════════════════════════════════════════════════════ --}}
<div class="login-card">

    {{-- Brand --}}
    <div class="login-brand">
        <div class="login-brand-icon">🏪</div>
        <span class="login-brand-title">Kiosko UNER</span>
        <span class="login-brand-subtitle">Ingresá con tu cuenta</span>
    </div>

    {{-- Error message --}}
    <div class="login-error {{ $errors->any() ? 'visible' : '' }}" id="loginError">
        <span class="login-error-icon">⚠️</span>
        <span id="loginErrorText">{{ $errors->first('login') ?: 'Credenciales incorrectas. Intentá de nuevo.' }}</span>
    </div>

    {{-- Login form --}}
    <form class="login-form" id="loginForm" action="{{ url('/login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label" for="login">Usuario o correo electrónico</label>
            <input
                class="form-input {{ $errors->has('login') ? 'error' : '' }}"
                type="text"
                id="login"
                name="login"
                value="{{ old('login') }}"
                placeholder="usuario o correo@uner.edu.ar"
                required
                autocomplete="username"
            >
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña</label>
            <input
                class="form-input"
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="form-row">
            <label class="form-checkbox-label">
                <input class="form-checkbox" type="checkbox" name="remember" id="remember">
                Recordarme
            </label>
        </div>

        <button class="btn-submit" type="submit" id="btnLogin">
            Ingresar
        </button>

    </form>

    {{-- Divider --}}
    <div class="login-divider">
        <div class="login-divider-line"></div>
        <span class="login-divider-text">o</span>
        <div class="login-divider-line"></div>
    </div>

    {{-- Footer --}}
    <div class="login-footer">
        <span class="login-footer-text">
            <a href="{{ url('/') }}" class="login-footer-link">← Volver al inicio</a>
        </span>
    </div>

</div>

<script src="{{ asset('js/login/login.js') }}"></script>
</body>
</html>
