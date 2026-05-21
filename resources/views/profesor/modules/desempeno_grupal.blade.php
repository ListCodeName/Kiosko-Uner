{{-- MÓDULO: DESEMPEÑO GRUPAL – Profesor --}}

{{-- Header --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño por Grupo</h2>
    <div class="perf-filters">
        <div id="grpSummaryBadge" class="perf-ind-total-badge" style="display:none"></div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECCIÓN: ESTADÍSTICA GLOBAL + PÓDIO
     ═══════════════════════════════════════════════════════════ --}}
<div id="grpGlobalSection" style="display:none">

    {{-- Estadísticas globales --}}
    <div class="grp-global-bar" id="grpGlobalBar"></div>

    {{-- Pódio --}}
    <div class="grp-podium-section">
        <div class="grp-section-title">
            <span>🏆</span> Pódio de Desempeño
            <span class="grp-section-sub">Alumnos más destacados de todos los grupos</span>
        </div>
        <div class="grp-podium-grid">
            {{-- Top Asistencia --}}
            <div class="grp-podium-col">
                <div class="grp-podium-col-header blue">
                    <span>📅</span> Top Asistencia
                </div>
                <div class="grp-podium-list" id="podiumAtt"></div>
            </div>
            {{-- Top Actividad --}}
            <div class="grp-podium-col">
                <div class="grp-podium-col-header green">
                    <span>⚡</span> Top Contribución
                </div>
                <div class="grp-podium-list" id="podiumAct"></div>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    <div class="grp-alerts-section" id="grpAlertsSection" style="display:none">
        <div class="grp-section-title">
            <span>⚠️</span> Atención requerida
            <span class="grp-section-sub">Alumnos por debajo de la media global</span>
        </div>
        <div class="grp-alerts-list" id="grpAlertsList"></div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════
     SECCIÓN: TARJETAS POR GRUPO
     ═══════════════════════════════════════════════════════════ --}}
<div id="grpCardsGrid" class="grp-cards-grid">
    {{-- Loaded dynamically --}}
    <div class="perf-ind-empty" id="grpEmpty">
        <div class="asist-spinner"></div>
    </div>
</div>
