<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel Profesor – Kiosko UNER</title>
    <meta name="description" content="Panel de gestión para profesores del sistema Kiosko UNER.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/superadmin/superadmin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profesor/profesor.css') }}">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="panel-layout">

    {{-- ════════════════════════════════════════ SIDEBAR ════════════════════════════════════════ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon" style="background:linear-gradient(135deg,#16a34a,#22c55e)">📚</div>
            <span class="sidebar-brand-text">Profesor</span>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-label">Principal</span>

            <button class="nav-item" data-module="inicio" data-title="Inicio" data-badge="Dashboard" data-tooltip="Inicio">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Inicio</span>
            </button>

            <div class="nav-divider"></div>
            <span class="nav-section-label">Gestión</span>

            <button class="nav-item" data-module="usuarios" data-title="Usuarios" data-badge="Gestión" data-tooltip="Usuarios">
                <span class="nav-icon">👤</span>
                <span class="nav-label">Usuarios</span>
            </button>

            <button class="nav-item" data-module="grupos" data-title="Grupos" data-badge="Gestión" data-tooltip="Grupos">
                <span class="nav-icon">👥</span>
                <span class="nav-label">Grupos</span>
            </button>

            <button class="nav-item" data-module="asistencia" data-title="Asistencia" data-badge="Gestión" data-tooltip="Asistencia">
                <span class="nav-icon">📋</span>
                <span class="nav-label">Asistencia</span>
            </button>

            <div class="nav-divider"></div>
            <span class="nav-section-label">Desempeño</span>

            <button class="nav-item" data-module="desempeno-individual" data-title="Desempeño Individual" data-badge="Análisis" data-tooltip="Individual">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Individual</span>
            </button>

            <button class="nav-item" data-module="desempeno-grupal" data-title="Desempeño Grupal" data-badge="Análisis" data-tooltip="Grupal">
                <span class="nav-icon">📈</span>
                <span class="nav-label">Grupal</span>
            </button>

            <button class="nav-item" data-module="desempeno-economico" data-title="Desempeño Económico" data-badge="Análisis" data-tooltip="Económico">
                <span class="nav-icon">💰</span>
                <span class="nav-label">Económico</span>
            </button>

        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-collapse" style="color:var(--text-secondary)">
                    <span class="btn-collapse-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span>
                    <span class="btn-collapse-label">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ════════════════════════════════════════ PANEL DERECHO ════════════════════════════════════════ --}}
    <div class="panel-right">
        <header class="panel-header">
            <button class="header-hamburger" id="btnHamburger" aria-label="Abrir menú">☰</button>
            <div class="header-breadcrumb">
                <span class="breadcrumb-root">Profesor</span>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current" id="headerTitle">Inicio</span>
            </div>
            <span class="header-module-badge" id="headerBadge">Dashboard</span>
            @php
                $user = Auth::user();
                $initials = $user
                    ? collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
                    : '?';
            @endphp
            <div class="header-actions">
                <div class="header-avatar" title="{{ $user->name ?? 'Profesor' }}" style="background:linear-gradient(135deg,#16a34a,#22c55e);border-color:rgba(34,197,94,.3)">{{ $initials }}</div>
            </div>
        </header>

        <main class="panel-content">
            <section class="module-section" data-module-content="inicio">
                @include('profesor.modules.inicio')
            </section>
            <section class="module-section" data-module-content="usuarios">
                @include('profesor.modules.usuarios')
            </section>
            <section class="module-section" data-module-content="grupos">
                @include('profesor.modules.grupos')
            </section>
            <section class="module-section" data-module-content="asistencia">
                @include('profesor.modules.asistencia')
            </section>
            <section class="module-section" data-module-content="desempeno-individual">
                @include('profesor.modules.desempeno_individual')
            </section>
            <section class="module-section" data-module-content="desempeno-grupal">
                @include('profesor.modules.desempeno_grupal')
            </section>
            <section class="module-section" data-module-content="desempeno-economico">
                @include('profesor.modules.desempeno_economico')
            </section>
        </main>
    </div>

</div>

<div class="toast" id="toast"></div>

<script src="{{ asset('js/modal-manager.js') }}"></script>
<script src="{{ asset('js/profesor/profesor.js') }}"></script>
<script src="{{ asset('js/profesor/grupos.js') }}"></script>
<script src="{{ asset('js/profesor/asistencia.js') }}"></script>
<script src="{{ asset('js/profesor/desempeno_individual.js') }}"></script>

<script src="{{ asset('js/profesor/usuarios.js') }}"></script>
</body>
</html>
