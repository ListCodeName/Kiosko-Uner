<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control – Kiosko UNER</title>
    <meta name="description" content="Panel de administración del sistema Kiosko UNER.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel/panel.css') }}">
</head>
<body>

{{-- Overlay mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="panel-layout">

    {{-- ════════════════════════════════════════
         SIDEBAR
    ════════════════════════════════════════ --}}
    <aside class="sidebar" id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">🏪</div>
            <span class="sidebar-brand-text">Kiosko UNER</span>
        </div>

        {{-- Navegación --}}
        <nav class="sidebar-nav">

            {{-- ── Principal ── --}}
            <span class="nav-section-label">Principal</span>

            <button class="nav-item"
                    data-module="inicio"
                    data-title="Inicio"
                    data-badge="Dashboard"
                    data-tooltip="Inicio">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Inicio</span>
            </button>

            <div class="nav-divider"></div>

            {{-- ── Subgrupo: Kiosko ── --}}
            <div class="nav-group open" id="group-kiosko">
                <button class="nav-group-toggle" data-group="kiosko">
                    <span class="nav-group-toggle-icon">▶</span>
                    <span class="nav-group-label">Kiosko</span>
                </button>
                <div class="nav-group-items">

                    <button class="nav-item"
                            data-module="compras"
                            data-title="Compras"
                            data-badge="Kiosko"
                            data-tooltip="Compras">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-label">Compras</span>
                    </button>

                    <button class="nav-item"
                            data-module="productos"
                            data-title="Productos"
                            data-badge="Kiosko"
                            data-tooltip="Productos">
                        <span class="nav-icon">📦</span>
                        <span class="nav-label">Productos</span>
                    </button>

                    <button class="nav-item"
                            data-module="ventas"
                            data-title="Ventas"
                            data-badge="Kiosko"
                            data-tooltip="Ventas">
                        <span class="nav-icon">💳</span>
                        <span class="nav-label">Ventas</span>
                    </button>

                    <button class="nav-item"
                            data-module="ingresos"
                            data-title="Ingresos"
                            data-badge="Kiosko"
                            data-tooltip="Ingresos">
                        <span class="nav-icon">📈</span>
                        <span class="nav-label">Ingresos</span>
                    </button>

                    <button class="nav-item"
                            data-module="egresos"
                            data-title="Egresos"
                            data-badge="Kiosko"
                            data-tooltip="Egresos">
                        <span class="nav-icon">📉</span>
                        <span class="nav-label">Egresos</span>
                    </button>

                    <button class="nav-item"
                            data-module="estadisticas"
                            data-title="Estadísticas"
                            data-badge="Kiosko"
                            data-tooltip="Estadísticas">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Estadísticas</span>
                    </button>

                </div>
            </div>

            <div class="nav-divider"></div>

            {{-- ── Subgrupo: Gestión de Personal ── --}}
            <div class="nav-group open" id="group-personal">
                <button class="nav-group-toggle" data-group="personal">
                    <span class="nav-group-toggle-icon">▶</span>
                    <span class="nav-group-label">Gestión de Personal</span>
                </button>
                <div class="nav-group-items">

                    <button class="nav-item"
                            data-module="empleados"
                            data-title="Empleados"
                            data-badge="Personal"
                            data-tooltip="Empleados">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label">Empleados</span>
                    </button>

                    <button class="nav-item"
                            data-module="usuarios"
                            data-title="Usuarios"
                            data-badge="Personal"
                            data-tooltip="Usuarios">
                        <span class="nav-icon">👤</span>
                        <span class="nav-label">Usuarios</span>
                    </button>

                </div>
            </div>

        </nav>

        {{-- Footer sidebar --}}
        <div class="sidebar-footer">
            <button class="btn-collapse" id="btnCollapse">
                <span class="btn-collapse-icon">◀</span>
                <span class="btn-collapse-label">Minimizar</span>
            </button>
        </div>

    </aside>

    {{-- ════════════════════════════════════════
         PANEL DERECHO
    ════════════════════════════════════════ --}}
    <div class="panel-right">

        {{-- HEADER --}}
        <header class="panel-header">
            <button class="header-hamburger" id="btnHamburger" aria-label="Abrir menú">☰</button>
            <div class="header-breadcrumb">
                <span class="breadcrumb-root">Kiosko UNER</span>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current" id="headerTitle">Inicio</span>
            </div>
            <span class="header-module-badge" id="headerBadge">Dashboard</span>
            <div class="header-actions">
                <div class="header-avatar" title="Administrador">A</div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="panel-content">

            <section class="module-section" data-module-content="inicio">
                @include('panel.modules.inicio')
            </section>

            <section class="module-section" data-module-content="compras">
                @include('panel.modules.compras')
            </section>

            <section class="module-section" data-module-content="productos">
                @include('panel.modules.productos')
            </section>

            <section class="module-section" data-module-content="ventas">
                @include('panel.modules.ventas')
            </section>

            <section class="module-section" data-module-content="ingresos">
                @include('panel.modules.ingresos')
            </section>

            <section class="module-section" data-module-content="egresos">
                @include('panel.modules.egresos')
            </section>

            <section class="module-section" data-module-content="estadisticas">
                @include('panel.modules.estadisticas')
            </section>

            <section class="module-section" data-module-content="empleados">
                @include('panel.modules.empleados')
            </section>

            <section class="module-section" data-module-content="usuarios">
                @include('panel.modules.usuarios')
            </section>

        </main>

    </div>{{-- /.panel-right --}}

</div>{{-- /.panel-layout --}}

<script src="{{ asset('js/panel/panel.js') }}"></script>
</body>
</html>
