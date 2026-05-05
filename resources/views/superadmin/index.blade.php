<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin – Kiosko UNER</title>
    <meta name="description" content="Panel de Super Administración del sistema Kiosko UNER.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/superadmin.css') }}">
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
            <div class="sidebar-brand-icon">⚙️</div>
            <span class="sidebar-brand-text">Super Admin</span>
        </div>

        {{-- Navegación --}}
        <nav class="sidebar-nav">

            <span class="nav-section-label">Panel</span>

            <button class="nav-item"
                    data-module="inicio"
                    data-title="Inicio"
                    data-badge="Dashboard"
                    data-tooltip="Inicio">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Inicio</span>
            </button>

            <button class="nav-item"
                    data-module="personal"
                    data-title="Gestión de Personal"
                    data-badge="Administración"
                    data-tooltip="Personal">
                <span class="nav-icon">👥</span>
                <span class="nav-label">Personal</span>
            </button>

            <div class="nav-divider"></div>

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
                <span class="breadcrumb-root">Super Admin</span>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current" id="headerTitle">Inicio</span>
            </div>
            <span class="header-module-badge" id="headerBadge">Dashboard</span>
            <div class="header-actions">
                <form action="{{ route('logout') }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-logout"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Salir</button>
                </form>
                @php
                    $user = Auth::user();
                    $initials = $user
                        ? collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
                        : '?';
                @endphp
                <div class="header-avatar" title="{{ $user->name ?? 'Super Admin' }}">{{ $initials }}</div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="panel-content">

            <section class="module-section" data-module-content="inicio">
                @include('superadmin.modules.inicio')
            </section>

            <section class="module-section" data-module-content="personal">
                @include('superadmin.modules.personal')
            </section>

        </main>

    </div>{{-- /.panel-right --}}

</div>{{-- /.panel-layout --}}

{{-- Toast --}}
<div class="toast" id="toast"></div>

<script src="{{ asset('js/superadmin/superadmin.js') }}"></script>
</body>
</html>
