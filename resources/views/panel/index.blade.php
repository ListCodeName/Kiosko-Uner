<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control – Kiosko UNER</title>
    <meta name="description" content="Panel de administración del sistema Kiosko UNER.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            data-module="kiosco"
                            data-title="Kiosco"
                            data-badge="Punto de Venta"
                            data-tooltip="Kiosco">
                        <span class="nav-icon">🏪</span>
                        <span class="nav-label">Kiosco</span>
                    </button>

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

                    <button class="nav-item"
                            data-module="pedidos"
                            data-title="Pedidos"
                            data-badge="Kiosko"
                            data-tooltip="Pedidos">
                        <span class="nav-icon">📋</span>
                        <span class="nav-label">Pedidos</span>
                    </button>

                    <button class="nav-item"
                            data-module="entregas"
                            data-title="Entregas"
                            data-badge="Kiosko"
                            data-tooltip="Entregas">
                        <span class="nav-icon">🚚</span>
                        <span class="nav-label">Entregas</span>
                    </button>

                    <button class="nav-item"
                            data-module="proveedores"
                            data-title="Proveedores"
                            data-badge="Kiosko"
                            data-tooltip="Proveedores">
                        <span class="nav-icon">🏭</span>
                        <span class="nav-label">Proveedores</span>
                    </button>
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
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="header-avatar" title="Cerrar sesión" style="border:none;cursor:pointer"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button>
                </form>
                @php
                    $user = Auth::user();
                    $initials = $user
                        ? collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
                        : '?';
                @endphp
                <div class="header-avatar" title="{{ $user->name ?? 'Usuario' }}">{{ $initials }}</div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="panel-content">

            <section class="module-section" data-module-content="inicio">
                @include('panel.modules.inicio')
            </section>

            <section class="module-section" data-module-content="kiosco">
                @include('panel.modules.kiosco')
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

            {{-- Módulo empleados eliminado: la gestión completa la realiza el Profesor --}}

            <section class="module-section" data-module-content="usuarios">
                @include('panel.modules.usuarios')
            </section>

            <section class="module-section" data-module-content="pedidos">
                @include('panel.modules.pedidos')
            </section>

            <section class="module-section" data-module-content="entregas">
                @include('panel.modules.entregas')
            </section>

            <section class="module-section" data-module-content="proveedores">
                @include('panel.modules.proveedores')
            </section>

        </main>

    </div>{{-- /.panel-right --}}

</div>{{-- /.panel-layout --}}

<script src="{{ asset('js/modal-manager.js') }}"></script>
<script src="{{ asset('js/panel/panel.js') }}"></script>
<script src="{{ asset('js/panel/kiosco.js') }}"></script>
<script src="{{ asset('js/panel/usuarios.js') }}"></script>
</body>
</html>
