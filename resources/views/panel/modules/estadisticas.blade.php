{{-- MÓDULO: ESTADÍSTICAS – Panel del Alumno --}}
{{-- Gráficos circulares de ventas, compras, ingresos y egresos --}}

<style>
/* ══════════════════════════════════════════════════
   ESTADÍSTICAS – Estilos
══════════════════════════════════════════════════ */

/* ── Layout principal ── */
.est-wrap {
    display: flex; flex-direction: column; gap: 28px;
}

/* ── Botón refrescar ── */
.est-header {
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.est-title {
    font-size: 1.1rem; font-weight: 700; color: var(--text-white);
    display: flex; align-items: center; gap: 10px;
}
.est-refresh-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(45,140,255,.1); border: 1px solid rgba(45,140,255,.25);
    color: var(--blue-light, #6ab4ff); border-radius: 8px;
    padding: 7px 16px; font-family: inherit; font-size: .82rem;
    font-weight: 600; cursor: pointer; transition: all .2s;
}
.est-refresh-btn:hover {
    background: rgba(45,140,255,.2); box-shadow: 0 0 14px rgba(45,140,255,.2);
}
.est-refresh-btn.spinning svg { animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── KPI Hero row (3 métricas clave grandes) ── */
.est-kpi-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
.est-kpi {
    background: var(--bg-card);
    border: 1px solid var(--border-dim);
    border-radius: 16px;
    padding: 22px 24px;
    display: flex; flex-direction: column; gap: 6px;
    position: relative; overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
}
.est-kpi:hover { border-color: var(--border-mid); box-shadow: 0 6px 24px rgba(0,0,0,.4); }
.est-kpi::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: 16px 16px 0 0;
}
.est-kpi--ganancia::before { background: linear-gradient(90deg, #22c55e, #4ade80); }
.est-kpi--perdida::before  { background: linear-gradient(90deg, #ef4444, #f87171); }
.est-kpi--margen::before   { background: linear-gradient(90deg, #2d8cff, #6ab4ff); }
.est-kpi-icon  { font-size: 1.6rem; margin-bottom: 4px; }
.est-kpi-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }
.est-kpi-value {
    font-size: 1.8rem; font-weight: 800; line-height: 1;
    letter-spacing: -.5px;
}
.est-kpi--ganancia .est-kpi-value { color: #4ade80; }
.est-kpi--perdida  .est-kpi-value { color: #f87171; }
.est-kpi--margen   .est-kpi-value { color: #6ab4ff; }
.est-kpi-sub { font-size: .75rem; color: var(--text-muted); margin-top: 2px; }
.est-kpi-glow {
    position: absolute; bottom: -30px; right: -20px;
    width: 100px; height: 100px; border-radius: 50%;
    opacity: .06; pointer-events: none;
}
.est-kpi--ganancia .est-kpi-glow { background: #22c55e; }
.est-kpi--perdida  .est-kpi-glow { background: #ef4444; }
.est-kpi--margen   .est-kpi-glow { background: #2d8cff; }

/* ── Pendiente alert strip ── */
.est-pending-strip {
    display: flex; align-items: center; gap: 14px;
    background: rgba(245,158,11,.07);
    border: 1px solid rgba(245,158,11,.22);
    border-radius: 12px; padding: 14px 20px; flex-wrap: wrap;
}
.est-pending-strip-icon { font-size: 1.4rem; flex-shrink: 0; }
.est-pending-strip-text { flex: 1; min-width: 200px; }
.est-pending-strip-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #fbbf24; margin-bottom: 3px; }
.est-pending-strip-desc  { font-size: .84rem; color: var(--text-secondary); }
.est-pending-kpis { display: flex; gap: 20px; flex-wrap: wrap; }
.est-pending-kpi  { display: flex; flex-direction: column; align-items: center; }
.est-pending-kpi-val  { font-size: 1.25rem; font-weight: 800; color: #fbbf24; }
.est-pending-kpi-lbl  { font-size: .65rem; text-transform: uppercase; letter-spacing: .6px; color: var(--text-muted); }

/* ── Grilla de gráficos ── */
.est-charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
.est-chart-card {
    background: var(--bg-card);
    border: 1px solid var(--border-dim);
    border-radius: 16px; padding: 22px;
    display: flex; flex-direction: column; gap: 20px;
    transition: border-color .2s, box-shadow .2s;
}
.est-chart-card:hover { border-color: var(--border-mid); box-shadow: 0 4px 20px rgba(0,0,0,.35); }
.est-chart-title {
    font-size: .85rem; font-weight: 700; color: var(--text-white);
    display: flex; align-items: center; gap: 8px;
}
.est-chart-subtitle { font-size: .73rem; color: var(--text-muted); margin-top: 2px; }

/* ── Donut SVG ── */
.est-donut-wrap {
    display: flex; align-items: center; gap: 22px; flex-wrap: wrap;
}
.est-donut-svg { flex-shrink: 0; }
.est-donut-legend { display: flex; flex-direction: column; gap: 10px; flex: 1; min-width: 120px; }
.est-legend-item { display: flex; align-items: center; gap: 9px; }
.est-legend-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}
.est-legend-info { display: flex; flex-direction: column; }
.est-legend-label { font-size: .73rem; color: var(--text-secondary); }
.est-legend-val   { font-size: .9rem; font-weight: 700; color: var(--text-white); }
.est-legend-pct   { font-size: .68rem; color: var(--text-muted); }

/* ── Tarjeta ancha (fila completa) ── */
.est-chart-card--wide {
    grid-column: 1 / -1;
}

/* ── Gráfico productos más vendidos ── */
.est-prod-header {
    display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px;
}
.est-prod-toggle {
    display: flex; gap: 6px; flex-shrink: 0;
}
.est-prod-toggle-btn {
    padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,.1);
    background: transparent; color: var(--text-muted); font-size: .72rem; font-weight: 600;
    cursor: pointer; transition: all .2s; font-family: inherit;
}
.est-prod-toggle-btn.active {
    background: rgba(45,140,255,.2); border-color: rgba(45,140,255,.4);
    color: #6ab4ff;
}
.est-prod-list { display: flex; flex-direction: column; gap: 10px; }
.est-prod-row {
    display: grid;
    grid-template-columns: 26px 1fr auto;
    align-items: center; gap: 10px;
}
.est-prod-rank {
    font-size: .7rem; font-weight: 800; color: var(--text-muted);
    text-align: center;
}
.est-prod-rank--top { font-size: 1rem; }
.est-prod-rank--num {
    width: 22px; height: 22px; border-radius: 50%;
    background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: .68rem; font-weight: 800; color: var(--text-muted);
    margin: 0 auto;
}
.est-prod-footer {
    margin-top: 10px; font-size: .7rem; color: var(--text-muted);
    text-align: center; letter-spacing: .3px;
}
.est-prod-info { display: flex; flex-direction: column; gap: 4px; }
.est-prod-name {
    font-size: .82rem; font-weight: 600; color: var(--text-white);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 260px;
}
.est-prod-track {
    height: 7px; border-radius: 4px;
    background: rgba(255,255,255,.05); overflow: hidden;
}
.est-prod-fill {
    height: 100%; border-radius: 4px;
    transition: width .7s cubic-bezier(.4,0,.2,1);
}
.est-prod-val {
    font-size: .82rem; font-weight: 700; color: var(--text-white);
    text-align: right; white-space: nowrap;
}
.est-prod-empty {
    text-align: center; padding: 32px; color: var(--text-muted); font-size: .85rem;
}

/* ── Mini barras de compras ── */
.est-bar-row {
    display: flex; align-items: center; gap: 10px; margin-bottom: 6px;
}
.est-bar-label { font-size: .73rem; color: var(--text-secondary); width: 130px; flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.est-bar-track { flex: 1; height: 8px; background: rgba(255,255,255,.06); border-radius: 4px; overflow: hidden; }
.est-bar-fill  { height: 100%; border-radius: 4px; transition: width .6s cubic-bezier(.4,0,.2,1); }
.est-bar-val   { font-size: .73rem; color: var(--text-muted); width: 60px; text-align: right; flex-shrink: 0; }

/* ── Sección de resumen financiero ── */
.est-summary-row {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;
}
.est-sum-card {
    background: var(--bg-surface); border: 1px solid var(--border-dim);
    border-radius: 12px; padding: 14px 16px;
    display: flex; flex-direction: column; gap: 4px;
}
.est-sum-label { font-size: .68rem; text-transform: uppercase; letter-spacing: .8px; color: var(--text-muted); font-weight: 700; }
.est-sum-val   { font-size: 1.1rem; font-weight: 800; color: var(--text-white); }
.est-sum-sub   { font-size: .68rem; color: var(--text-muted); }

/* ── Loading state ── */
.est-loading {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 80px 20px; gap: 16px;
    color: var(--text-muted); font-size: .9rem;
}
.est-spinner {
    width: 36px; height: 36px; border: 3px solid rgba(45,140,255,.15);
    border-top-color: #2d8cff; border-radius: 50%;
    animation: spin .8s linear infinite;
}

/* ── Responsive ── */
@media (max-width: 900px) {
    .est-kpi-row       { grid-template-columns: repeat(3, 1fr); }
    .est-charts-grid   { grid-template-columns: 1fr; }
    .est-chart-card--wide { grid-column: auto; }
    .est-summary-row   { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .est-kpi-row     { grid-template-columns: 1fr; }
    .est-summary-row { grid-template-columns: 1fr; }
}
</style>

<div class="est-wrap" id="estWrap">

    {{-- ── Header ── --}}
    <div class="est-header">
        <div class="est-title">
            📊 Estadísticas del Kiosko
        </div>
        <button class="est-refresh-btn" id="estRefreshBtn" onclick="EstadisticasModule.load()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
            </svg>
            Actualizar
        </button>
    </div>

    {{-- ── Loading ── --}}
    <div class="est-loading" id="estLoading">
        <div class="est-spinner"></div>
        Cargando estadísticas…
    </div>

    {{-- ── Contenido (oculto hasta cargar) ── --}}
    <div id="estContent" style="display:none; display:flex; flex-direction:column; gap:28px;">

        {{-- KPIs Hero --}}
        <div class="est-kpi-row">
            <div class="est-kpi est-kpi--ganancia">
                <div class="est-kpi-glow"></div>
                <div class="est-kpi-icon">📈</div>
                <div class="est-kpi-label">Ganancia Efectiva</div>
                <div class="est-kpi-value" id="kpiGanancia">$0</div>
                <div class="est-kpi-sub" id="kpiGananciaSub">Ventas cobradas + Ingresos efectuados</div>
            </div>
            <div class="est-kpi est-kpi--perdida">
                <div class="est-kpi-glow"></div>
                <div class="est-kpi-icon">📉</div>
                <div class="est-kpi-label">Pérdida Efectiva</div>
                <div class="est-kpi-value" id="kpiPerdida">$0</div>
                <div class="est-kpi-sub" id="kpiPerdidaSub">Compras + Egresos efectuados</div>
            </div>
            <div class="est-kpi est-kpi--margen">
                <div class="est-kpi-glow"></div>
                <div class="est-kpi-icon" id="kpiMargenIcon">💰</div>
                <div class="est-kpi-label">Balance Neto</div>
                <div class="est-kpi-value" id="kpiMargen">$0</div>
                <div class="est-kpi-sub" id="kpiMargenSub">Ganancia − Pérdida</div>
            </div>
        </div>

        {{-- Tira de pendientes --}}
        <div class="est-pending-strip" id="estPendingStrip">
            <div class="est-pending-strip-icon">⏳</div>
            <div class="est-pending-strip-text">
                <div class="est-pending-strip-label">Margen Pendiente de Resolución</div>
                <div class="est-pending-strip-desc">Ventas, ingresos y egresos aún no confirmados que podrían impactar el balance.</div>
            </div>
            <div class="est-pending-kpis">
                <div class="est-pending-kpi">
                    <span class="est-pending-kpi-val" id="pendVentas">$0</span>
                    <span class="est-pending-kpi-lbl">Ventas pend.</span>
                </div>
                <div class="est-pending-kpi">
                    <span class="est-pending-kpi-val" id="pendIngresos">$0</span>
                    <span class="est-pending-kpi-lbl">Ingresos pend.</span>
                </div>
                <div class="est-pending-kpi">
                    <span class="est-pending-kpi-val" id="pendEgresos" style="color:#f87171">$0</span>
                    <span class="est-pending-kpi-lbl">Egresos pend.</span>
                </div>
                <div class="est-pending-kpi">
                    <span class="est-pending-kpi-val" id="pendNeto">$0</span>
                    <span class="est-pending-kpi-lbl">Neto pend.</span>
                </div>
            </div>
        </div>

        {{-- Resumen financiero en 4 columnas --}}
        <div class="est-summary-row">
            <div class="est-sum-card">
                <div class="est-sum-label">💳 Total Ventas</div>
                <div class="est-sum-val" id="sumVentasTotal">$0</div>
                <div class="est-sum-sub" id="sumVentasSub">0 operaciones</div>
            </div>
            <div class="est-sum-card">
                <div class="est-sum-label">🛒 Total Compras</div>
                <div class="est-sum-val" id="sumComprasTotal">$0</div>
                <div class="est-sum-sub" id="sumComprasSub">0 operaciones</div>
            </div>
            <div class="est-sum-card">
                <div class="est-sum-label">📈 Total Ingresos</div>
                <div class="est-sum-val" id="sumIngresosTotal">$0</div>
                <div class="est-sum-sub" id="sumIngresosSub">0 registros</div>
            </div>
            <div class="est-sum-card">
                <div class="est-sum-label">📉 Total Egresos</div>
                <div class="est-sum-val" id="sumEgresosTotal">$0</div>
                <div class="est-sum-sub" id="sumEgresosSub">0 registros</div>
            </div>
        </div>

        {{-- Grilla de gráficos ── --}}
        <div class="est-charts-grid">

            {{-- Gráfico 1: Estado de Ventas --}}
            <div class="est-chart-card">
                <div>
                    <div class="est-chart-title">💳 Ventas por Estado</div>
                    <div class="est-chart-subtitle">Distribución entre cobradas y pendientes</div>
                </div>
                <div class="est-donut-wrap">
                    <svg class="est-donut-svg" width="130" height="130" viewBox="0 0 130 130" id="donutVentas">
                        <circle cx="65" cy="65" r="50" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="18"/>
                        <!-- segmentos se renderizan por JS -->
                    </svg>
                    <div class="est-donut-legend" id="legendVentas"></div>
                </div>
            </div>

            {{-- Gráfico 2: Ventas por Método de Pago --}}
            <div class="est-chart-card">
                <div>
                    <div class="est-chart-title">💵 Método de Pago</div>
                    <div class="est-chart-subtitle">Efectivo vs Transferencia (ventas cobradas)</div>
                </div>
                <div class="est-donut-wrap">
                    <svg class="est-donut-svg" width="130" height="130" viewBox="0 0 130 130" id="donutMetodo">
                        <circle cx="65" cy="65" r="50" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="18"/>
                    </svg>
                    <div class="est-donut-legend" id="legendMetodo"></div>
                </div>
            </div>

            {{-- Gráfico 3: Ingresos por Categoría --}}
            <div class="est-chart-card">
                <div>
                    <div class="est-chart-title">📈 Ingresos por Categoría</div>
                    <div class="est-chart-subtitle">Desglose de fuentes de ingreso efectuadas</div>
                </div>
                <div class="est-donut-wrap">
                    <svg class="est-donut-svg" width="130" height="130" viewBox="0 0 130 130" id="donutIngresos">
                        <circle cx="65" cy="65" r="50" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="18"/>
                    </svg>
                    <div class="est-donut-legend" id="legendIngresos"></div>
                </div>
            </div>

            {{-- Gráfico 4: Egresos por Categoría --}}
            <div class="est-chart-card">
                <div>
                    <div class="est-chart-title">📉 Egresos por Categoría</div>
                    <div class="est-chart-subtitle">Desglose de gastos efectuados</div>
                </div>
                <div class="est-donut-wrap">
                    <svg class="est-donut-svg" width="130" height="130" viewBox="0 0 130 130" id="donutEgresos">
                        <circle cx="65" cy="65" r="50" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="18"/>
                    </svg>
                    <div class="est-donut-legend" id="legendEgresos"></div>
                </div>
            </div>

            {{-- Gráfico 5: Flujo de caja global (ancho completo) --}}
            <div class="est-chart-card est-chart-card--wide">
                <div>
                    <div class="est-chart-title">⚖️ Flujo de Caja — Ingresos vs Egresos</div>
                    <div class="est-chart-subtitle">Comparativa porcentual entre todas las entradas y salidas de dinero efectivas</div>
                </div>
                <div class="est-donut-wrap">
                    <svg class="est-donut-svg" width="130" height="130" viewBox="0 0 130 130" id="donutFlujo">
                        <circle cx="65" cy="65" r="50" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="18"/>
                    </svg>
                    <div style="flex:1; display:flex; flex-direction:column; gap: 10px;" id="legendFlujo"></div>
                </div>
            </div>

            {{-- Gráfico 6: Productos más vendidos (ancho completo) --}}
            <div class="est-chart-card est-chart-card--wide">
                <div class="est-prod-header">
                    <div>
                        <div class="est-chart-title">🏆 Productos Más Vendidos</div>
                        <div class="est-chart-subtitle">Ranking de los 10 productos con mayor movimiento en ventas cobradas</div>
                    </div>
                    <div class="est-prod-toggle">
                        <button class="est-prod-toggle-btn active" id="prodToggleQty" onclick="EstadisticasModule.showProdChart('qty')">📦 Cantidad</button>
                        <button class="est-prod-toggle-btn" id="prodToggleRev" onclick="EstadisticasModule.showProdChart('rev')">💰 Ingresos</button>
                    </div>
                </div>
                <div class="est-prod-list" id="prodChartList"></div>
                <div class="est-prod-footer" id="prodChartFooter" style="display:none"></div>
            </div>

        </div>{{-- /.est-charts-grid --}}

    </div>{{-- /#estContent --}}
</div>{{-- /.est-wrap --}}

<script>
/* ════════════════════════════════════════════════════════════════
   EstadisticasModule — Gráficos circulares del Panel del Alumno
   Consume: /panel/api/ventas, /panel/api/ingresos, /panel/api/egresos
════════════════════════════════════════════════════════════════ */
const EstadisticasModule = (function () {
    'use strict';

    // ── Utilidades ───────────────────────────────────────────────
    const $ = id => document.getElementById(id);

    function fmt(n) {
        return '$' + Number(n || 0).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function norm(n) { return Number(n) || 0; }

    // Paletas de colores por gráfico
    const PALETTES = {
        ventas:   ['#4ade80', '#fbbf24', '#a78bfa'],
        metodo:   ['#2d8cff', '#a78bfa'],
        ingresos: ['#34d399', '#6ee7b7', '#a7f3d0', '#fbbf24', '#fb923c', '#94a3b8'],
        egresos:  ['#f87171', '#fca5a5', '#fb923c', '#fbbf24', '#a78bfa', '#94a3b8'],
        flujo:    ['#4ade80', '#f87171'],
    };

    // ── Motor de gráfico donut SVG ───────────────────────────────
    function buildDonut(svgId, legendId, segments, palette) {
        const svg    = $(svgId);
        const legend = $(legendId);
        if (!svg || !legend) return;

        // Limpiar excepto el círculo de fondo (primer hijo)
        while (svg.children.length > 1) svg.removeChild(svg.lastChild);
        legend.innerHTML = '';

        const total = segments.reduce((s, x) => s + x.value, 0);
        if (total === 0) {
            // Estado vacío
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', '65'); text.setAttribute('y', '69');
            text.setAttribute('text-anchor', 'middle'); text.setAttribute('font-size', '11');
            text.setAttribute('fill', 'rgba(255,255,255,.25)'); text.setAttribute('font-family', 'Inter,sans-serif');
            text.textContent = 'Sin datos';
            svg.appendChild(text);
            return;
        }

        const R = 50, CX = 65, CY = 65, SW = 18;
        const C = 2 * Math.PI * R; // circunferencia

        // Texto central: total
        const centerText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        centerText.setAttribute('x', CX); centerText.setAttribute('y', CY + 4);
        centerText.setAttribute('text-anchor', 'middle');
        centerText.setAttribute('font-size', '11'); centerText.setAttribute('font-weight', '700');
        centerText.setAttribute('fill', 'rgba(255,255,255,.7)'); centerText.setAttribute('font-family', 'Inter,sans-serif');
        centerText.textContent = segments.length + ' seg.';
        svg.appendChild(centerText);

        let offset = 0;
        segments.forEach((seg, i) => {
            const color   = (palette || PALETTES.ventas)[i % palette.length];
            const pct     = seg.value / total;
            const dash    = pct * C;
            const gap     = C - dash;

            const circle  = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            circle.setAttribute('cx', CX); circle.setAttribute('cy', CY); circle.setAttribute('r', R);
            circle.setAttribute('fill', 'none');
            circle.setAttribute('stroke', color);
            circle.setAttribute('stroke-width', SW);
            circle.setAttribute('stroke-dasharray', `${dash} ${gap}`);
            circle.setAttribute('stroke-dashoffset', -(offset));
            circle.setAttribute('transform', `rotate(-90 ${CX} ${CY})`);
            circle.style.transition = 'stroke-dasharray .6s cubic-bezier(.4,0,.2,1)';
            svg.appendChild(circle);

            // Gap visual entre segmentos (1.5px)
            offset += dash + (total > 0 && segments.length > 1 ? (1.5 / C) * C : 0);

            // Leyenda
            const item = document.createElement('div');
            item.className = 'est-legend-item';
            item.innerHTML = `
                <div class="est-legend-dot" style="background:${color}"></div>
                <div class="est-legend-info">
                    <span class="est-legend-label">${seg.label}</span>
                    <span class="est-legend-val">${seg.formatted}</span>
                    <span class="est-legend-pct">${(pct * 100).toFixed(1)}%</span>
                </div>`;
            legend.appendChild(item);
        });
    }

    // ── Estado gráfico de productos ──────────────────────────────
    let _prodDataQty  = [];
    let _prodDataRev  = [];
    let _prodView     = 'qty';
    let _prodTotalQty = 0;   // total de productos distintos (sin slice)
    let _prodTotalRev = 0;

    // ── Gráfico de productos más vendidos ───────────────────────
    function buildProdChart(mode) {
        const list = $('prodChartList');
        if (!list) return;

        const data      = mode === 'qty' ? _prodDataQty : _prodDataRev;
        const totalDist = mode === 'qty' ? _prodTotalQty : _prodTotalRev;
        list.innerHTML  = '';

        if (!data.length) {
            list.innerHTML = '<div class="est-prod-empty">📭 Sin datos de productos vendidos</div>';
            return;
        }

        const maxVal = data[0]?.value || 1;

        // Colores fijos del podio
        const podiumColors = [
            'linear-gradient(90deg, #fbbf24, #f59e0b)', // 🥇 oro
            'linear-gradient(90deg, #94a3b8, #cbd5e1)', // 🥈 plata
            'linear-gradient(90deg, #fb923c, #c2612a)', // 🥉 bronce
        ];
        // Paleta continua para el resto (posiciones 4-10)
        const restColors = [
            'linear-gradient(90deg, #2d8cff, #6ab4ff)',
            'linear-gradient(90deg, #4ade80, #22c55e)',
            'linear-gradient(90deg, #a78bfa, #8b5cf6)',
            'linear-gradient(90deg, #34d399, #10b981)',
            'linear-gradient(90deg, #60a5fa, #3b82f6)',
            'linear-gradient(90deg, #f472b6, #ec4899)',
            'linear-gradient(90deg, #c084fc, #a855f7)',
        ];

        data.forEach((item, i) => {
            const pct      = (item.value / maxVal) * 100;
            const rank     = i + 1;
            const isPodium = rank <= 3;
            const grad     = isPodium ? podiumColors[i] : restColors[(i - 3) % restColors.length];
            const displayVal = mode === 'qty'
                ? `${item.value} ud.`
                : fmt(item.value);

            // Separador visual entre podio y el resto
            if (rank === 4) {
                const sep = document.createElement('div');
                sep.style.cssText = 'border-top: 1px solid rgba(255,255,255,.07); margin: 4px 0 2px;';
                list.appendChild(sep);
            }

            const medalIcon = rank === 1 ? '🥇' : rank === 2 ? '🥈' : rank === 3 ? '🥉' : null;
            const rankHTML  = medalIcon
                ? `<div class="est-prod-rank est-prod-rank--top">${medalIcon}</div>`
                : `<div class="est-prod-rank est-prod-rank--num">${rank}</div>`;

            const row = document.createElement('div');
            row.className = 'est-prod-row';
            row.innerHTML = `
                ${rankHTML}
                <div class="est-prod-info">
                    <span class="est-prod-name" title="${item.label}">${item.label}</span>
                    <div class="est-prod-track">
                        <div class="est-prod-fill" style="width:${pct.toFixed(1)}%; background:${grad}"></div>
                    </div>
                </div>
                <div class="est-prod-val">${displayVal}</div>`;
            list.appendChild(row);
        });

        // Footer: mostrar cuántos hay en total si supera el límite
        const footer = $('prodChartFooter');
        if (footer) {
            if (totalDist > 10) {
                footer.textContent = `Mostrando top 10 de ${totalDist} productos distintos vendidos`;
                footer.style.display = 'block';
            } else {
                footer.style.display = 'none';
            }
        }
    }

    function showProdChart(mode) {
        _prodView = mode;
        $('prodToggleQty')?.classList.toggle('active', mode === 'qty');
        $('prodToggleRev')?.classList.toggle('active', mode === 'rev');
        buildProdChart(mode);
    }

    // ── Fetch paralelo de todas las fuentes ─────────────────────
    async function fetchAll() {
        const [vRes, iRes, eRes, cRes] = await Promise.all([
            fetch('/panel/api/ventas'),
            fetch('/panel/api/ingresos'),
            fetch('/panel/api/egresos'),
            fetch('/api/compras'),
        ]);

        if (!vRes.ok || !iRes.ok || !eRes.ok || !cRes.ok) throw new Error('Error al obtener datos');

        const [vData, iData, eData, cData] = await Promise.all([
            vRes.json(), iRes.json(), eRes.json(), cRes.json()
        ]);

        // Ventas: el endpoint devuelve un array directo (sin wrapper)
        // Ingresos: { ingresos: [...] }
        // Egresos:  { egresos: [...] }
        // Compras:  { compras: [...] }
        return {
            ventas:   Array.isArray(vData) ? vData : (vData.sales ?? vData.ventas ?? []),
            ingresos: iData.ingresos ?? [],
            egresos:  eData.egresos  ?? [],
            compras:  cData.compras  ?? [],
        };
    }

    // ── Motor principal ──────────────────────────────────────────
    async function load() {
        const loading = $('estLoading');
        const content = $('estContent');
        const btn     = $('estRefreshBtn');

        if (loading) loading.style.display = 'flex';
        if (content) content.style.display = 'none';
        if (btn)     btn.classList.add('spinning');

        try {
            const { ventas, ingresos, egresos, compras } = await fetchAll();

            // ── Ventas ───────────────────────────────────────────
            // API devuelve: { total, metodo, estado } por item
            const vPagado    = ventas.filter(v => v.estado === 'pagado');
            const vPendiente = ventas.filter(v => v.estado === 'pendiente');

            const vPagadoMonto    = vPagado.reduce((s, v) => s + norm(v.total), 0);
            const vPendienteMonto = vPendiente.reduce((s, v) => s + norm(v.total), 0);
            const vTotal          = vPagadoMonto + vPendienteMonto;

            // Método de pago (solo cobradas) — campo: `metodo`
            const vEfectivo      = vPagado.filter(v => v.metodo === 'efectivo');
            const vTransferencia = vPagado.filter(v => v.metodo === 'transferencia');
            const vEfectivoMonto = vEfectivo.reduce((s, v) => s + norm(v.total), 0);
            const vTransMonto    = vTransferencia.reduce((s, v) => s + norm(v.total), 0);

            // ── Ingresos (Desduplicación) ─────────────────────────
            const iEfectuadosTodos = ingresos.filter(i => i.estado === 'efectuado');
            const iPendientesTodos = ingresos.filter(i => i.estado === 'pendiente');

            // Filtrar las ventas automáticas de la tabla ingresos
            const iEfectuadosSinVentas = iEfectuadosTodos.filter(i => i.tipo !== 'venta_kiosco');
            const iPendientesSinVentas = iPendientesTodos.filter(i => i.tipo !== 'venta_kiosco');

            const iEfectuadoMontoSinVentas = iEfectuadosSinVentas.reduce((s, i) => s + norm(i.monto), 0);
            const iPendienteMontoSinVentas = iPendientesSinVentas.reduce((s, i) => s + norm(i.monto), 0);

            const iEfectuadoMontoTodos = iEfectuadosTodos.reduce((s, i) => s + norm(i.monto), 0);
            const iPendienteMontoTodos = iPendientesTodos.reduce((s, i) => s + norm(i.monto), 0);
            const iTotalTodos          = iEfectuadoMontoTodos + iPendienteMontoTodos;

            // Categorías ingresos (solo efectuados, sin duplicar ventas)
            const iByTipo = {};
            iEfectuadosSinVentas.forEach(i => {
                const k = i.tipo ?? 'otro';
                iByTipo[k] = (iByTipo[k] || 0) + norm(i.monto);
            });

            // ── Egresos (Desduplicación) ──────────────────────────
            const eEfectuadosTodos = egresos.filter(e => e.estado === 'efectuado');
            const ePendientesTodos = egresos.filter(e => e.estado === 'pendiente');

            // Filtrar las compras automáticas de la tabla egresos
            const eEfectuadosSinCompras = eEfectuadosTodos.filter(e => !e.descripcion.startsWith('Compra mercadería #'));
            const ePendientesSinCompras = ePendientesTodos.filter(e => !e.descripcion.startsWith('Compra mercadería #'));

            const eEfectuadoMontoSinCompras = eEfectuadosSinCompras.reduce((s, e) => s + norm(e.monto), 0);
            const ePendienteMontoSinCompras = ePendientesSinCompras.reduce((s, e) => s + norm(e.monto), 0);

            const eEfectuadoMontoTodos = eEfectuadosTodos.reduce((s, e) => s + norm(e.monto), 0);
            const ePendienteMontoTodos = ePendientesTodos.reduce((s, e) => s + norm(e.monto), 0);
            const eTotalTodos          = eEfectuadoMontoTodos + ePendienteMontoTodos;

            // Categorías egresos (solo efectuados, sin duplicar compras)
            const eByTipo = {};
            eEfectuadosSinCompras.forEach(e => {
                const k = e.tipo ?? 'otro';
                eByTipo[k] = (eByTipo[k] || 0) + norm(e.monto);
            });

            // ── Compras ──
            const comprasTotal = compras.reduce((s, c) => s + norm(c.total), 0);

            // ── KPIs Hero ────────────────────────────────────────
            // Ganancia Efectiva: Ventas reales cobradas + Ingresos manuales efectuados
            const gananciaEfectiva = vPagadoMonto + iEfectuadoMontoSinVentas;
            // Pérdida Efectiva: Compras reales + Egresos manuales efectuados
            const perdidaEfectiva  = comprasTotal + eEfectuadoMontoSinCompras;
            const balanceNeto      = gananciaEfectiva - perdidaEfectiva;

            $('kpiGanancia').textContent = fmt(gananciaEfectiva);
            $('kpiGananciaSub').textContent =
                `Ventas cobradas ${fmt(vPagadoMonto)} + Ingresos manuales ${fmt(iEfectuadoMontoSinVentas)}`;

            $('kpiPerdida').textContent = fmt(perdidaEfectiva);
            $('kpiPerdidaSub').textContent =
                `Compras ${fmt(comprasTotal)} + Egresos manuales ${fmt(eEfectuadoMontoSinCompras)}`;

            $('kpiMargen').textContent = fmt(Math.abs(balanceNeto));
            $('kpiMargen').style.color = balanceNeto >= 0 ? '#4ade80' : '#f87171';
            $('kpiMargenIcon').textContent = balanceNeto >= 0 ? '💰' : '🔴';
            $('kpiMargenSub').textContent  = balanceNeto >= 0 ? 'Superávit neto' : 'Déficit neto';
            const margenCard = $('kpiMargen').closest('.est-kpi');
            if (margenCard) {
                margenCard.querySelector('.est-kpi-glow').style.background = balanceNeto >= 0 ? '#22c55e' : '#ef4444';
                margenCard.style.setProperty('--kpi-bar', balanceNeto >= 0 ? 'linear-gradient(90deg,#22c55e,#4ade80)' : 'linear-gradient(90deg,#ef4444,#f87171)');
            }

            // ── Pendientes (Ventas pendientes + Ingresos manuales pendientes - Egresos manuales pendientes) ──
            const pendNeto = vPendienteMonto + iPendienteMontoSinVentas - ePendienteMontoSinCompras;
            $('pendVentas').textContent   = fmt(vPendienteMonto);
            $('pendIngresos').textContent = fmt(iPendienteMontoSinVentas);
            $('pendEgresos').textContent  = fmt(ePendienteMontoSinCompras);
            $('pendNeto').textContent     = fmt(Math.abs(pendNeto));
            $('pendNeto').style.color     = pendNeto >= 0 ? '#fbbf24' : '#f87171';

            // ── Resumen 4-col ────────────────────────────────────
            $('sumVentasTotal').textContent  = fmt(vTotal);
            $('sumVentasSub').textContent    = `${ventas.length} operación(es)`;
            $('sumComprasTotal').textContent = fmt(comprasTotal);
            $('sumComprasSub').textContent   = `${compras.length} compra(s)`;
            $('sumIngresosTotal').textContent = fmt(iTotalTodos);
            $('sumIngresosSub').textContent   = `${ingresos.length} registro(s)`;
            $('sumEgresosTotal').textContent  = fmt(eTotalTodos);
            $('sumEgresosSub').textContent    = `${egresos.length} registro(s)`;

            // ── Gráfico 1: Ventas por estado ─────────────────────
            buildDonut('donutVentas', 'legendVentas', [
                { label: '✅ Cobradas',  value: vPagadoMonto,    formatted: fmt(vPagadoMonto) },
                { label: '⏳ Pendientes', value: vPendienteMonto, formatted: fmt(vPendienteMonto) },
            ].filter(s => s.value > 0), PALETTES.ventas);

            // ── Gráfico 2: Método de pago ─────────────────────────
            buildDonut('donutMetodo', 'legendMetodo', [
                { label: '💵 Efectivo',       value: vEfectivoMonto, formatted: fmt(vEfectivoMonto) },
                { label: '📲 Transferencia',  value: vTransMonto,    formatted: fmt(vTransMonto) },
            ].filter(s => s.value > 0), PALETTES.metodo);

            // ── Gráfico 3: Ingresos por categoría ────────────────
            const tipoLabels = {
                activo_no_comestible: 'Activo no com.',
                excedente_caja:       'Excedente caja',
                donacion:             'Donación',
                subvencion:           'Subvención',
                ingreso_excepcional:  'Ing. excepcional',
                otro:                 'Otro',
            };
            const iSegs = Object.entries(iByTipo)
                .map(([k, v]) => ({ label: tipoLabels[k] ?? k, value: v, formatted: fmt(v) }))
                .sort((a, b) => b.value - a.value);
            buildDonut('donutIngresos', 'legendIngresos', iSegs.length ? iSegs : [], PALETTES.ingresos);

            // ── Gráfico 4: Egresos por categoría ─────────────────
            const etipoLabels = {
                gasto_operativo: 'Gasto operativo',
                pasivo:          'Pasivo / Deuda',
                insumos:         'Insumos',
                servicio:        'Servicios',
                impuesto:        'Impuesto / Tasa',
                otro:            'Otro',
            };
            const eSegs = Object.entries(eByTipo)
                .map(([k, v]) => ({ label: etipoLabels[k] ?? k, value: v, formatted: fmt(v) }))
                .sort((a, b) => b.value - a.value);
            buildDonut('donutEgresos', 'legendEgresos', eSegs.length ? eSegs : [], PALETTES.egresos);

            // ── Gráfico 5: Flujo de caja global ──────────────────
            buildDonut('donutFlujo', 'legendFlujo', [
                { label: '📈 Entradas efectivas',  value: gananciaEfectiva, formatted: fmt(gananciaEfectiva) },
                { label: '📉 Salidas efectivas',   value: perdidaEfectiva,  formatted: fmt(perdidaEfectiva) },
            ].filter(s => s.value > 0), PALETTES.flujo);

            // ── Gráfico 6: Productos más vendidos ────────────────
            // Acumular cantidades e ingresos por producto desde los items de ventas cobradas
            const prodQtyMap = {};
            const prodRevMap = {};
            ventas
                .filter(v => v.estado === 'pagado')
                .forEach(v => {
                    (v.items ?? []).forEach(item => {
                        const nombre = item.nombre || `Producto #${item.product_id}`;
                        prodQtyMap[nombre] = (prodQtyMap[nombre] || 0) + norm(item.cantidad);
                        prodRevMap[nombre] = (prodRevMap[nombre] || 0) + (norm(item.cantidad) * norm(item.precio));
                    });
                });

            const allQty = Object.entries(prodQtyMap)
                .map(([label, value]) => ({ label, value }))
                .sort((a, b) => b.value - a.value);
            const allRev = Object.entries(prodRevMap)
                .map(([label, value]) => ({ label, value }))
                .sort((a, b) => b.value - a.value);

            _prodTotalQty = allQty.length;
            _prodTotalRev = allRev.length;
            _prodDataQty  = allQty.slice(0, 10);
            _prodDataRev  = allRev.slice(0, 10);

            buildProdChart(_prodView);

            // ── Mostrar contenido ─────────────────────────────────
            if (loading) loading.style.display = 'none';
            if (content) content.style.display = 'flex';

        } catch (err) {
            console.error('[EstadisticasModule]', err);
            if (loading) loading.innerHTML =
                '<div style="color:#f87171;font-size:.9rem">⚠️ Error al cargar estadísticas. Intentá de nuevo.</div>';
        } finally {
            if (btn) btn.classList.remove('spinning');
        }
    }

    // Cargar cuando el módulo es visible por primera vez
    let loaded = false;
    function initOnVisible() {
        const section = document.querySelector('[data-module-content="estadisticas"]');
        if (!section) { load(); return; }

        const obs = new MutationObserver(() => {
            if (section.classList.contains('active') && !loaded) {
                loaded = true;
                load();
            }
        });
        obs.observe(section, { attributes: true, attributeFilter: ['class'] });

        // Si ya está activo al cargar
        if (section.classList.contains('active')) { loaded = true; load(); }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnVisible);
    } else {
        initOnVisible();
    }

    return { load, showProdChart };
})();
</script>
