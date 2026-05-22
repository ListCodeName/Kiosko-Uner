{{-- MÓDULO: DESEMPEÑO ECONÓMICO – Profesor --}}

<style>
/* Estilos para el filtro de rango de fechas */
.perf-filters {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.filter-date-group {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--card-bg);
    border: 1px solid var(--border-dim);
    padding: 4px 12px;
    border-radius: 6px;
    height: 38px;
}
.filter-date-group label {
    font-size: .7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 600;
}
.filter-input-date {
    background: transparent;
    border: none;
    color: var(--text);
    font-size: .85rem;
    outline: none;
    cursor: pointer;
    font-family: inherit;
}
.filter-input-date::-webkit-calendar-picker-indicator {
    filter: invert(1);
    opacity: 0.6;
    cursor: pointer;
}
.filter-input-date::-webkit-calendar-picker-indicator:hover {
    opacity: 0.9;
}

/* Skeletons Animados (Premium Shimmer) */
.econ-card.skeleton {
    position: relative;
    overflow: hidden;
    pointer-events: none;
    min-height: 340px;
}
.skeleton-title, .skeleton-sub, .skeleton-circle, .skeleton-line {
    background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
}
@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.skeleton-title {
    height: 18px;
    width: 60%;
    margin-bottom: 10px;
}
.skeleton-sub {
    height: 12px;
    width: 40%;
    margin-bottom: 24px;
}
.skeleton-chart-container {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}
.skeleton-circle {
    height: 100px;
    width: 100px;
    border-radius: 50%;
    flex-shrink: 0;
}
.skeleton-legend {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.skeleton-line {
    height: 12px;
    width: 100%;
}
.skeleton-summary {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--border-dim);
}

/* Alert Indicator Style */
.alert-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 6px;
    color: #f87171;
    cursor: help;
}
.econ-card.deficit {
    border-color: rgba(239, 68, 68, 0.25) !important;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.05);
}
.econ-card.deficit::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: #ef4444;
}

/* Export hover styles */
#btnExportEcon:hover {
    background: rgba(255,255,255,0.05) !important;
    border-color: var(--text-muted) !important;
    color: var(--text) !important;
}
</style>

{{-- Header con filtros --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño Económico por Grupo</h2>
    <div class="perf-filters">
        {{-- Rápido --}}
        <select class="filter-select" id="econPeriod" style="height:38px">
            <option value="day">Hoy</option>
            <option value="month" selected>Este mes</option>
            <option value="year">Este año</option>
            <option value="custom">Rango personalizado</option>
        </select>
        
        {{-- Inputs de Rango --}}
        <div class="filter-date-group">
            <label for="econStartDate">Desde</label>
            <input type="date" class="filter-input-date" id="econStartDate">
        </div>
        <div class="filter-date-group">
            <label for="econEndDate">Hasta</label>
            <input type="date" class="filter-input-date" id="econEndDate">
        </div>

        {{-- Exportar --}}
        <button id="btnExportEcon" title="Exportar reporte de desempeño económico" style="background:var(--card-bg);border:1px solid var(--border-dim);color:var(--text-muted);padding:8px 14px;border-radius:6px;cursor:pointer;display:flex;align-items:center;gap:6px;font-size:.85rem;transition:all .2s ease;height:38px">
            <span style="font-size:1rem">📥</span> Exportar
        </button>
    </div>
</div>

{{-- Resumen general histórico consolidado --}}
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-card-icon green">💰</div>
        <div class="stat-card-info">
            <span class="stat-card-value" id="histGanancia">$0</span>
            <span class="stat-card-label">Ingresos Totales (Histórico)</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:var(--red-bg);border:1px solid var(--red-border)">📉</div>
        <div class="stat-card-info">
            <span class="stat-card-value" id="histPerdida">$0</span>
            <span class="stat-card-label">Egresos Totales (Histórico)</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon blue">📊</div>
        <div class="stat-card-info">
            <span class="stat-card-value" id="histBalance">$0</span>
            <span class="stat-card-label">Balance Neto (Histórico)</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple">📈</div>
        <div class="stat-card-info">
            <span class="stat-card-value" id="histMargen">0%</span>
            <span class="stat-card-label">Margen de Ganancia (Histórico)</span>
        </div>
    </div>
</div>

{{-- Contenedor de Skeletons (Cargando) --}}
<div class="econ-grid" id="econSkeleton">
    <div class="econ-card skeleton">
        <div class="skeleton-title"></div>
        <div class="skeleton-sub"></div>
        <div class="skeleton-chart-container">
            <div class="skeleton-circle"></div>
            <div class="skeleton-legend">
                <div class="skeleton-line" style="width:85%"></div>
                <div class="skeleton-line" style="width:70%"></div>
                <div class="skeleton-line" style="width:90%"></div>
            </div>
        </div>
        <div class="skeleton-summary">
            <div class="skeleton-line" style="width:95%"></div>
            <div class="skeleton-line" style="width:90%"></div>
        </div>
    </div>
    <div class="econ-card skeleton">
        <div class="skeleton-title"></div>
        <div class="skeleton-sub"></div>
        <div class="skeleton-chart-container">
            <div class="skeleton-circle"></div>
            <div class="skeleton-legend">
                <div class="skeleton-line" style="width:80%"></div>
                <div class="skeleton-line" style="width:65%"></div>
                <div class="skeleton-line" style="width:85%"></div>
            </div>
        </div>
        <div class="skeleton-summary">
            <div class="skeleton-line" style="width:90%"></div>
            <div class="skeleton-line" style="width:95%"></div>
        </div>
    </div>
    <div class="econ-card skeleton">
        <div class="skeleton-title"></div>
        <div class="skeleton-sub"></div>
        <div class="skeleton-chart-container">
            <div class="skeleton-circle"></div>
            <div class="skeleton-legend">
                <div class="skeleton-line" style="width:90%"></div>
                <div class="skeleton-line" style="width:75%"></div>
                <div class="skeleton-line" style="width:80%"></div>
            </div>
        </div>
        <div class="skeleton-summary">
            <div class="skeleton-line" style="width:95%"></div>
            <div class="skeleton-line" style="width:85%"></div>
        </div>
    </div>
</div>

{{-- Contenedor de Tarjetas de Grupo Reales --}}
<div class="econ-grid" id="econGrid" style="display:none">
    {{-- Renderizado dinámico vía JavaScript --}}
</div>

{{-- Mensaje de no hay grupos --}}
<div id="econEmptyState" style="display:none;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:var(--card-bg);border:1px solid var(--border-dim);border-radius:12px;text-align:center;gap:12px;margin-top:20px">
    <span style="font-size:3rem">📂</span>
    <h3 style="margin:0;color:var(--text);font-size:1.1rem">No se encontraron grupos a cargo</h3>
    <p style="margin:0;color:var(--text-muted);font-size:.9rem;max-width:320px">Creá o asigná grupos desde la pestaña de «Gestión de Grupos» para visualizar su desempeño económico.</p>
</div>
