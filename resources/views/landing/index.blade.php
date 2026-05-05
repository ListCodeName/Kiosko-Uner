<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosko UNER – Sistema de Gestión</title>
    <meta name="description" content="Sistema de gestión integral para el Kiosko de la Universidad Nacional de Entre Ríos. Control de ventas, compras, stock y personal.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing/landing.css') }}">
</head>
<body>

{{-- ════════════════════════════════════════════════════════════
     HEADER / NAVBAR
     ════════════════════════════════════════════════════════════ --}}
<header class="landing-header" id="landingHeader">

    {{-- Brand --}}
    <a href="{{ url('/') }}" class="header-brand">
        <div class="header-brand-icon">🏪</div>
        <span class="header-brand-name">Kiosko UNER</span>
    </a>

    {{-- Nav links (placeholders para futuro) --}}
    <nav class="header-nav" id="headerNav">
        <a href="#funcionalidades" class="header-nav-link">Funcionalidades</a>
        <a href="#nosotros" class="header-nav-link">Nosotros</a>
        <a href="#contacto" class="header-nav-link">Contacto</a>
        <a href="{{ url('/login') }}" class="btn-login" id="btnIngresar">
            <span class="btn-login-icon">🔐</span>
            Ingresar
        </a>
    </nav>

    {{-- Mobile toggle --}}
    <button class="header-mobile-toggle" id="mobileToggle" aria-label="Abrir menú">☰</button>

</header>


{{-- ════════════════════════════════════════════════════════════
     HERO SECTION
     ════════════════════════════════════════════════════════════ --}}
<section class="hero" id="hero">

    {{-- Floating particles --}}
    <div class="hero-particles">
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
        <div class="hero-particle"></div>
    </div>

    <div class="hero-content">

        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Universidad Nacional de Entre Ríos
        </div>

        <h1 class="hero-title">
            Gestión inteligente para tu
            <span class="hero-title-highlight">Kiosko Universitario</span>
        </h1>

        <p class="hero-subtitle">
            Controlá ventas, compras, stock y personal desde un solo lugar.
            Un sistema moderno diseñado para simplificar la operación diaria.
        </p>

        <div class="hero-actions">
            <a href="{{ url('/login') }}" class="btn-hero-primary" id="heroLoginBtn">
                🔐 Ingresar al Sistema
            </a>
            <a href="#funcionalidades" class="btn-hero-secondary">
                📋 Ver Funcionalidades
            </a>
        </div>

    </div>
</section>


{{-- ════════════════════════════════════════════════════════════
     STATS BAR (Placeholder – datos futuros)
     ════════════════════════════════════════════════════════════ --}}
<div class="stats-bar fade-in-up">
    <div class="stats-bar-inner">
        <div class="stat-item stat-placeholder">
            <div class="stat-value"></div>
            <span class="stat-label">Productos</span>
        </div>
        <div class="stat-item stat-placeholder">
            <div class="stat-value"></div>
            <span class="stat-label">Ventas del mes</span>
        </div>
        <div class="stat-item stat-placeholder">
            <div class="stat-value"></div>
            <span class="stat-label">Empleados</span>
        </div>
        <div class="stat-item stat-placeholder">
            <div class="stat-value"></div>
            <span class="stat-label">Ingresos totales</span>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════════
     FUNCIONALIDADES (Features)
     ════════════════════════════════════════════════════════════ --}}
<section class="landing-section" id="funcionalidades">

    <div class="section-header fade-in-up">
        <div class="section-label">🚀 Módulos del Sistema</div>
        <h2 class="section-title">Todo lo que necesitás, en un solo panel</h2>
        <p class="section-subtitle">
            Cada módulo fue diseñado para cubrir una necesidad específica
            de la gestión diaria del kiosko.
        </p>
    </div>

    <div class="features-grid">

        {{-- ── Feature: Compras ── --}}
        <div class="feature-card fade-in-up">
            <div class="feature-icon">🛒</div>
            <h3 class="feature-title">Registro de Compras</h3>
            <p class="feature-desc">
                Registrá cada compra a proveedores con detalle de precios,
                cantidades y fechas para un control total del abastecimiento.
            </p>
        </div>

        {{-- ── Feature: Productos ── --}}
        <div class="feature-card fade-in-up">
            <div class="feature-icon">📦</div>
            <h3 class="feature-title">Gestión de Productos</h3>
            <p class="feature-desc">
                Mantené un catálogo organizado con categorías, precios de venta
                y alertas de stock bajo para no quedarte sin mercadería.
            </p>
        </div>

        {{-- ── Feature: Ventas ── --}}
        <div class="feature-card fade-in-up">
            <div class="feature-icon">💳</div>
            <h3 class="feature-title">Control de Ventas</h3>
            <p class="feature-desc">
                Registrá ventas en tiempo real con resumen diario, métodos de pago
                y generación de tickets o comprobantes.
            </p>
        </div>

        {{-- ── Feature: Ingresos/Egresos ── --}}
        <div class="feature-card fade-in-up">
            <div class="feature-icon">📊</div>
            <h3 class="feature-title">Ingresos y Egresos</h3>
            <p class="feature-desc">
                Visualizá el flujo de dinero del negocio con gráficos claros,
                balances mensuales y reportes exportables.
            </p>
        </div>

        {{-- ── Feature: Empleados (Placeholder) ── --}}
        <div class="feature-card placeholder fade-in-up">
            <div class="feature-icon">👥</div>
            <h3 class="feature-title">Gestión de Empleados</h3>
            <p class="feature-desc">
                Administrá turnos, horarios y datos del personal del kiosko.
                Módulo en desarrollo.
            </p>
            <div class="placeholder-tag">🔧 Próximamente</div>
        </div>

        {{-- ── Feature: Estadísticas (Placeholder) ── --}}
        <div class="feature-card placeholder fade-in-up">
            <div class="feature-icon">📈</div>
            <h3 class="feature-title">Estadísticas Avanzadas</h3>
            <p class="feature-desc">
                Dashboards con métricas clave: productos más vendidos,
                horarios pico y proyecciones de venta.
            </p>
            <div class="placeholder-tag">🔧 Próximamente</div>
        </div>

    </div>

</section>


{{-- ════════════════════════════════════════════════════════════
     NOSOTROS (Placeholder)
     ════════════════════════════════════════════════════════════ --}}
<section class="landing-section" id="nosotros">

    <div class="section-header fade-in-up">
        <div class="section-label">🎓 Proyecto Académico</div>
        <h2 class="section-title">¿Quiénes somos?</h2>
        <p class="section-subtitle">
            Somos estudiantes de la UNER desarrollando una solución real
            para la gestión del kiosko universitario.
        </p>
    </div>

    {{-- Placeholder: Aquí se puede agregar tarjetas del equipo, fotos, etc --}}
    <div class="features-grid" style="max-width: 740px;">

        <div class="feature-card placeholder fade-in-up">
            <div class="feature-icon">👤</div>
            <h3 class="feature-title">Miembro del Equipo</h3>
            <p class="feature-desc">
                Información sobre los integrantes del equipo de desarrollo.
                Biografía, rol y contacto.
            </p>
            <div class="placeholder-tag">📝 Contenido pendiente</div>
        </div>

        <div class="feature-card placeholder fade-in-up">
            <div class="feature-icon">🏫</div>
            <h3 class="feature-title">Sobre el Proyecto</h3>
            <p class="feature-desc">
                Contexto académico, objetivos del sistema y
                tecnologías utilizadas en el desarrollo.
            </p>
            <div class="placeholder-tag">📝 Contenido pendiente</div>
        </div>

    </div>

</section>


{{-- ════════════════════════════════════════════════════════════
     CTA / CONTACTO (Empty state)
     ════════════════════════════════════════════════════════════ --}}
<section class="cta-section" id="contacto">

    <div class="cta-card fade-in-up">

        <div class="cta-illustration">
            <div class="cta-pulse"></div>
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 12h32v24H8z" stroke="#2d8cff" stroke-width="2" rx="3" opacity="0.7"/>
                <path d="M8 12l16 14 16-14" stroke="#6ab4ff" stroke-width="2" stroke-linecap="round" opacity="0.8"/>
                <line x1="8" y1="36" x2="18" y2="26" stroke="#2d8cff" stroke-width="1.5" opacity="0.4"/>
                <line x1="40" y1="36" x2="30" y2="26" stroke="#2d8cff" stroke-width="1.5" opacity="0.4"/>
            </svg>
        </div>

        <h2 class="cta-title">¿Tenés alguna consulta?</h2>
        <p class="cta-desc">
            Este módulo de contacto está en desarrollo. Próximamente vas a poder
            enviar tus consultas directamente desde aquí.
        </p>

        <a href="{{ url('/login') }}" class="btn-hero-primary">
            🔐 Ingresar al Sistema
        </a>

    </div>

</section>


{{-- ════════════════════════════════════════════════════════════
     FOOTER
     ════════════════════════════════════════════════════════════ --}}
<footer class="landing-footer">
    <span class="footer-text">© {{ date('Y') }} Kiosko UNER – Todos los derechos reservados.</span>
    <div class="footer-links">
        <a href="#funcionalidades" class="footer-link">Funcionalidades</a>
        <a href="#nosotros" class="footer-link">Nosotros</a>
        <a href="#contacto" class="footer-link">Contacto</a>
    </div>
</footer>


<script src="{{ asset('js/landing/landing.js') }}"></script>
</body>
</html>
