{{-- ============================================================
     MÓDULO: INICIO / DASHBOARD
     Panel de Control – Kiosko UNER
     ============================================================ --}}

<div class="empty-state">

    {{-- Ilustración --}}
    <div class="empty-state-illustration">
        <div class="empty-state-pulse"></div>
        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
            {{-- Gráfico de barras --}}
            <rect x="6"  y="30" width="8"  height="18" rx="2" fill="#2d8cff" opacity="0.5"/>
            <rect x="18" y="20" width="8"  height="28" rx="2" fill="#2d8cff" opacity="0.7"/>
            <rect x="30" y="10" width="8"  height="38" rx="2" fill="#2d8cff" opacity="0.9"/>
            <rect x="42" y="22" width="8"  height="26" rx="2" fill="#2d8cff" opacity="0.6"/>
            {{-- Línea base --}}
            <line x1="4" y1="50" x2="52" y2="50" stroke="#2d8cff" stroke-width="1.5" stroke-opacity="0.3"/>
            {{-- Punto superior --}}
            <circle cx="34" cy="8" r="3" fill="#6ab4ff"/>
        </svg>
    </div>

    {{-- Texto --}}
    <h2 class="empty-state-title">Bienvenido al panel de control</h2>
    <p class="empty-state-desc">
        Todavía no hay datos para mostrar. Comenzá configurando
        los módulos del sistema desde el menú lateral.
    </p>

    {{-- Acción --}}
    <button class="btn btn-gen">
        ⚙ Configurar sistema
    </button>

</div>
