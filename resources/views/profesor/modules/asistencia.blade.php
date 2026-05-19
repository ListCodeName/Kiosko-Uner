{{-- MÓDULO: ASISTENCIA – Profesor --}}

{{-- ── Header con controles ───────────────────────────── --}}
<div class="asist-controls">

    {{-- Selector de grupo --}}
    <div class="asist-group-selector">
        <label class="form-label" for="asistGroupSelect">Grupo</label>
        <select class="form-select asist-select" id="asistGroupSelect">
            <option value="">— Seleccioná un grupo —</option>
            {{-- Cargado dinámicamente por JS --}}
        </select>
    </div>

    {{-- Navegador de semana --}}
    <div class="week-nav" id="weekNav" aria-label="Navegación de semanas">
        <button class="week-nav-btn" id="btnPrevWeek" title="Semana anterior">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="week-nav-label" id="weekLabel">
            <span class="week-nav-range" id="weekRangeText">Cargando...</span>
            <span class="week-nav-sub" id="weekYearText"></span>
        </div>
        <button class="week-nav-btn" id="btnNextWeek" title="Semana siguiente">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <button class="week-nav-today" id="btnCurrentWeek" title="Ir a la semana actual">Hoy</button>
    </div>

</div>

{{-- ── Resumen rápido de la semana --}}
<div class="asist-week-stats" id="asistWeekStats" style="display:none">
    <div class="asist-stat-pill" id="statTotalDays">
        <span class="asist-stat-icon">📅</span>
        <span id="statDaysLabel">5 días</span>
    </div>
    <div class="asist-stat-pill present">
        <span class="asist-stat-icon">✓</span>
        <span id="statPresentPct">— presentes</span>
    </div>
    <div class="asist-stat-pill absent">
        <span class="asist-stat-icon">✗</span>
        <span id="statAbsentPct">— ausentes</span>
    </div>
    <div class="asist-stat-pill unmarked">
        <span class="asist-stat-icon">○</span>
        <span id="statUnmarkedPct">— sin marcar</span>
    </div>
</div>

{{-- ── Tabla de asistencia --}}
<div class="asist-table-wrapper" id="asistTableWrapper">

    {{-- Estado vacío inicial --}}
    <div class="asist-empty" id="asistEmpty">
        <div class="asist-empty-icon">📋</div>
        <div class="asist-empty-title">Seleccioná un grupo</div>
        <div class="asist-empty-sub">Elegí un grupo para ver y gestionar la asistencia semanal</div>
    </div>

    {{-- Loading --}}
    <div class="asist-loading" id="asistLoading" style="display:none">
        <div class="asist-spinner"></div>
        <span>Cargando asistencias...</span>
    </div>

    {{-- Tabla (generada por JS) --}}
    <div id="asistTableContainer" style="display:none">
        <table class="attendance-table" id="attendanceTable">
            <thead id="attendanceThead"></thead>
            <tbody id="attendanceTbody"></tbody>
        </table>
    </div>

</div>
