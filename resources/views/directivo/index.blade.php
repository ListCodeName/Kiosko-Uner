<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Directivo – Kiosko UNER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel/panel.css') }}">
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="panel-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">🏛️</div>
            <span class="sidebar-brand-text">Directivo</span>
        </div>
        <nav class="sidebar-nav">
            <span class="nav-section-label">Panel</span>
            <button class="nav-item active" data-module="inicio" data-title="Inicio" data-badge="Directivo" data-tooltip="Inicio">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Inicio</span>
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
    <div class="panel-right">
        <header class="panel-header">
            <button class="header-hamburger" id="btnHamburger" aria-label="Abrir menú">☰</button>
            <div class="header-breadcrumb">
                <span class="breadcrumb-root">Directivo</span>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current">Inicio</span>
            </div>
            <span class="header-module-badge">Directivo</span>
            @php
                $user = Auth::user();
                $initials = $user
                    ? collect(explode(' ', $user->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
                    : '?';
            @endphp
            <div class="header-actions">
                <div class="header-avatar" title="{{ $user->name ?? 'Directivo' }}">{{ $initials }}</div>
            </div>
        </header>
        <main class="panel-content">
            <section class="module-section active">
                <div class="empty-state">
                    <div class="empty-state-illustration">
                        <div class="empty-state-pulse"></div>
                        <svg width="56" height="56" viewBox="0 0 56 56" fill="none"><path d="M28 8l20 12v16L28 48 8 36V20L28 8z" stroke="#2d8cff" stroke-width="2" opacity=".7"/><circle cx="28" cy="28" r="8" stroke="#6ab4ff" stroke-width="1.5" opacity=".6"/><circle cx="28" cy="28" r="3" fill="#2d8cff" opacity=".8"/></svg>
                    </div>
                    <h2 class="empty-state-title">Panel Directivo</h2>
                    <p class="empty-state-desc">Este panel se encuentra en desarrollo. Próximamente vas a poder gestionar la institución, reportes y configuraciones avanzadas.</p>
                </div>
            </section>
        </main>
    </div>
</div>
<script src="{{ asset('js/panel/panel.js') }}"></script>
</body>
</html>
