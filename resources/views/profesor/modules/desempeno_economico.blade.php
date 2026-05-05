{{-- MÓDULO: DESEMPEÑO ECONÓMICO – Profesor --}}

{{-- Header con filtros --}}
<div class="perf-header">
    <h2 class="perf-title">Desempeño Económico por Grupo</h2>
    <div class="perf-filters">
        <select class="filter-select" id="econGroup">
            <option value="">Todos los grupos</option>
            <option value="a">Grupo A - Mañana</option>
            <option value="b">Grupo B - Tarde</option>
            <option value="c">Grupo C - Noche</option>
        </select>
        <select class="filter-select" id="econPeriod">
            <option value="day">Hoy</option>
            <option value="month" selected>Este mes</option>
            <option value="year">Este año</option>
        </select>
    </div>
</div>

{{-- Resumen general --}}
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-card-icon green">💰</div>
        <div class="stat-card-info">
            <span class="stat-card-value">$148.500</span>
            <span class="stat-card-label">Ventas + Ingresos</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon" style="background:var(--red-bg);border:1px solid var(--red-border)">📉</div>
        <div class="stat-card-info">
            <span class="stat-card-value">$92.300</span>
            <span class="stat-card-label">Compras + Egresos</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon blue">📊</div>
        <div class="stat-card-info">
            <span class="stat-card-value" style="color:var(--green)">$56.200</span>
            <span class="stat-card-label">Balance Neto</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon purple">📈</div>
        <div class="stat-card-info">
            <span class="stat-card-value">61.7%</span>
            <span class="stat-card-label">Margen</span>
        </div>
    </div>
</div>

{{-- Economic cards grid --}}
<div class="econ-grid">

    {{-- Grupo A --}}
    <div class="econ-card">
        <div class="econ-card-title">🟢 Grupo A - Mañana</div>
        <div class="econ-card-sub">6 alumnos · Mayo 2026</div>

        <div class="pie-chart-container">
            <div class="pie-chart" style="background: conic-gradient(#22c55e 0deg 216deg, #f04040 216deg 360deg)">
                <div class="pie-chart-center">
                    <span class="pie-chart-center-value">60%</span>
                    <span class="pie-chart-center-label">Ingreso</span>
                </div>
            </div>
            <div class="pie-chart-legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background:#22c55e"></span>
                    Ventas + Ingresos
                    <span class="legend-value">$68.200</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:#f04040"></span>
                    Compras + Egresos
                    <span class="legend-value">$45.400</span>
                </div>
                <div class="legend-item" style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border-dim)">
                    <span class="legend-dot" style="background:var(--blue-neon)"></span>
                    Balance
                    <span class="legend-value" style="color:var(--green)">+$22.800</span>
                </div>
            </div>
        </div>

        <div class="econ-summary">
            <div class="econ-summary-item">
                <span class="econ-summary-label">Ventas</span>
                <span class="econ-summary-value positive">$52.000</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Compras</span>
                <span class="econ-summary-value negative">$31.200</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros ingresos</span>
                <span class="econ-summary-value positive">$16.200</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros egresos</span>
                <span class="econ-summary-value negative">$14.200</span>
            </div>
        </div>
    </div>

    {{-- Grupo B --}}
    <div class="econ-card">
        <div class="econ-card-title">🔵 Grupo B - Tarde</div>
        <div class="econ-card-sub">5 alumnos · Mayo 2026</div>

        <div class="pie-chart-container">
            <div class="pie-chart" style="background: conic-gradient(#22c55e 0deg 194deg, #f04040 194deg 360deg)">
                <div class="pie-chart-center">
                    <span class="pie-chart-center-value">54%</span>
                    <span class="pie-chart-center-label">Ingreso</span>
                </div>
            </div>
            <div class="pie-chart-legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background:#22c55e"></span>
                    Ventas + Ingresos
                    <span class="legend-value">$42.800</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:#f04040"></span>
                    Compras + Egresos
                    <span class="legend-value">$36.500</span>
                </div>
                <div class="legend-item" style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border-dim)">
                    <span class="legend-dot" style="background:var(--blue-neon)"></span>
                    Balance
                    <span class="legend-value" style="color:var(--green)">+$6.300</span>
                </div>
            </div>
        </div>

        <div class="econ-summary">
            <div class="econ-summary-item">
                <span class="econ-summary-label">Ventas</span>
                <span class="econ-summary-value positive">$30.500</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Compras</span>
                <span class="econ-summary-value negative">$24.800</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros ingresos</span>
                <span class="econ-summary-value positive">$12.300</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros egresos</span>
                <span class="econ-summary-value negative">$11.700</span>
            </div>
        </div>
    </div>

    {{-- Grupo C --}}
    <div class="econ-card">
        <div class="econ-card-title">🟡 Grupo C - Noche</div>
        <div class="econ-card-sub">4 alumnos · Mayo 2026</div>

        <div class="pie-chart-container">
            <div class="pie-chart" style="background: conic-gradient(#22c55e 0deg 245deg, #f04040 245deg 360deg)">
                <div class="pie-chart-center">
                    <span class="pie-chart-center-value">72%</span>
                    <span class="pie-chart-center-label">Ingreso</span>
                </div>
            </div>
            <div class="pie-chart-legend">
                <div class="legend-item">
                    <span class="legend-dot" style="background:#22c55e"></span>
                    Ventas + Ingresos
                    <span class="legend-value">$37.500</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot" style="background:#f04040"></span>
                    Compras + Egresos
                    <span class="legend-value">$10.400</span>
                </div>
                <div class="legend-item" style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border-dim)">
                    <span class="legend-dot" style="background:var(--blue-neon)"></span>
                    Balance
                    <span class="legend-value" style="color:var(--green)">+$27.100</span>
                </div>
            </div>
        </div>

        <div class="econ-summary">
            <div class="econ-summary-item">
                <span class="econ-summary-label">Ventas</span>
                <span class="econ-summary-value positive">$28.000</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Compras</span>
                <span class="econ-summary-value negative">$7.200</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros ingresos</span>
                <span class="econ-summary-value positive">$9.500</span>
            </div>
            <div class="econ-summary-item">
                <span class="econ-summary-label">Otros egresos</span>
                <span class="econ-summary-value negative">$3.200</span>
            </div>
        </div>
    </div>

</div>
