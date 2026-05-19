{{-- MÓDULO: DESEMPEÑO INDIVIDUAL – Profesor --}}

{{-- Header --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño Individual de Alumnos</h2>
    <div class="perf-filters">
        <div id="perfIndSummaryBadge" class="perf-ind-total-badge" style="display:none"></div>
    </div>
</div>

{{-- Cards grid --}}
<div class="perf-grid" id="perfIndGrid">
    <div class="perf-ind-empty" id="perfIndEmpty">
        <div style="font-size:2rem;margin-bottom:8px">📊</div>
        <div style="font-size:.9rem;font-weight:600;color:var(--text-white)">Seleccioná un grupo</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:4px">Elegí un grupo para ver el desempeño de sus alumnos</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL: DETALLE DE ASISTENCIA (calendario mensual)
     ══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalAttendanceDetail">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="attDetailTitle">Asistencia detallada</h3>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px" id="attDetailSub"></div>
            </div>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <div class="modal-body">

            {{-- Navegador de mes --}}
            <div class="att-month-nav">
                <button class="week-nav-btn" id="attMonthPrev" title="Mes anterior">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <span class="att-month-label" id="attMonthLabel">Cargando...</span>
                <button class="week-nav-btn" id="attMonthNext" title="Mes siguiente">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            {{-- Stats rápidas del mes --}}
            <div class="att-month-stats" id="attMonthStats"></div>

            {{-- Calendario compacto --}}
            <div id="attCalendarLoading" class="asist-loading" style="min-height:120px;display:none">
                <div class="asist-spinner"></div>
            </div>
            <div id="attCalendarWrap" style="display:none">
                {{-- Header de días de semana --}}
                <div class="att-cal-header">
                    <span>Lun</span><span>Mar</span><span>Mié</span>
                    <span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span>
                </div>
                <div class="att-calendar-grid" id="attCalendarGrid"></div>
            </div>

            {{-- Leyenda --}}
            <div class="att-legend">
                <span class="att-legend-item present">✓ Presente</span>
                <span class="att-legend-item absent">✗ Ausente</span>
                <span class="att-legend-item unmarked">— Sin marcar</span>
                <span class="att-legend-item offmonth">· Fuera del mes</span>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL: DETALLE DE PARTICIPACIÓN ACTIVA
     ══════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalActivityDetail">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" id="actDetailTitle">Participación activa</h3>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px" id="actDetailSub"></div>
            </div>
            <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('visible')">✕</button>
        </div>
        <div class="modal-body">

            {{-- Loading --}}
            <div id="actDetailLoading" class="asist-loading" style="min-height:80px;display:none">
                <div class="asist-spinner"></div>
            </div>

            {{-- Resumen por tipo --}}
            <div id="actDetailContent" style="display:none">
                <div class="act-type-grid" id="actTypeGrid"></div>

                <div style="margin-top:18px">
                    <div style="font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
                        Últimas acciones registradas
                    </div>
                    <div class="activity-log-list" id="actLogList"></div>
                </div>
            </div>

            {{-- Vacío --}}
            <div id="actDetailEmpty" class="asist-empty" style="display:none;min-height:80px">
                <div class="asist-empty-icon">📭</div>
                <div class="asist-empty-title">Sin actividad registrada</div>
                <div class="asist-empty-sub">Este alumno no tiene acciones en el sistema aún</div>
            </div>

        </div>
    </div>
</div>
