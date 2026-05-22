/**
 * Módulo: Desempeño Económico (Profesor)
 */
const DesempenoEconomico = (() => {
    const $ = id => document.getElementById(id);

    // Formateador de moneda
    const fmt = val => new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 2
    }).format(val || 0);

    // Formateador de fechas para inputs HTML (YYYY-MM-DD)
    const fmtDate = date => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };

    function init() {
        const periodSelect = $('econPeriod');
        const startInput   = $('econStartDate');
        const endInput     = $('econEndDate');
        const exportBtn    = $('btnExportEcon');

        if (!periodSelect || !startInput || !endInput) return;

        // Establecer fechas por defecto para "Este mes"
        setDatesForPeriod('month');

        // Escuchar cambios en selector rápido
        periodSelect.addEventListener('change', () => {
            const val = periodSelect.value;
            if (val !== 'custom') {
                setDatesForPeriod(val);
                load();
            }
        });

        // Escuchar cambios en inputs de fecha
        const onDateChange = () => {
            periodSelect.value = 'custom';
            load();
        };

        startInput.addEventListener('change', onDateChange);
        endInput.addEventListener('change', onDateChange);

        // Exportar a PDF / Impresión premium
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                window.print();
            });
        }
    }

    function setDatesForPeriod(period) {
        const today = new Date();
        let start, end;

        switch (period) {
            case 'day':
                start = today;
                end = today;
                break;
            case 'month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'year':
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 12, 0);
                break;
        }

        if (start && end) {
            $('econStartDate').value = fmtDate(start);
            $('econEndDate').value = fmtDate(end);
        }
    }

    async function load() {
        const skeleton   = $('econSkeleton');
        const grid       = $('econGrid');
        const emptyState = $('econEmptyState');

        const start = $('econStartDate').value;
        const end   = $('econEndDate').value;

        if (skeleton) skeleton.style.display = 'grid';
        if (grid) grid.style.display = 'none';
        if (emptyState) emptyState.style.display = 'none';

        try {
            const res = await fetch(`/profesor/api/performance/economico?start_date=${start}&end_date=${end}`);
            const data = await res.json();

            if (!data.success) {
                throw new Error(data.message || 'Error al obtener rendimiento económico.');
            }

            // 1. Rellenar Banners Históricos (Consolidados)
            const totals = data.totals;
            $('histGanancia').textContent = fmt(totals.ganancia_efectiva);
            $('histPerdida').textContent  = fmt(totals.perdida_efectiva);
            
            const balanceNeto = totals.balance_neto;
            const balanceEl = $('histBalance');
            balanceEl.textContent = fmt(Math.abs(balanceNeto));
            if (balanceNeto >= 0) {
                balanceEl.style.color = 'var(--green)';
                balanceEl.textContent = fmt(balanceNeto);
            } else {
                balanceEl.style.color = '#f87171';
                balanceEl.textContent = '-' + fmt(Math.abs(balanceNeto));
            }

            const margenEl = $('histMargen');
            margenEl.textContent = `${totals.margen}%`;
            if (totals.margen >= 0) {
                margenEl.style.color = 'var(--green)';
            } else {
                margenEl.style.color = '#f87171';
            }

            // 2. Rellenar Grilla de Grupos
            const groups = data.groups;
            if (!groups || groups.length === 0) {
                if (skeleton) skeleton.style.display = 'none';
                if (emptyState) emptyState.style.display = 'flex';
                return;
            }

            grid.innerHTML = ''; // Limpiar grilla anterior

            groups.forEach(g => {
                const card = document.createElement('div');
                card.className = `econ-card ${g.balance_neto < 0 ? 'deficit' : ''}`;
                
                const isDeficit = g.balance_neto < 0;
                const balanceValHtml = isDeficit 
                    ? `<span class="legend-value" style="color:#f87171">-${fmt(Math.abs(g.balance_neto))} <span class="alert-icon-wrap" title="Este grupo presenta pérdidas en el período seleccionado">⚠️</span></span>`
                    : `<span class="legend-value" style="color:var(--green)">+${fmt(g.balance_neto)}</span>`;

                const angle = (g.pct_ingreso / 100) * 360;
                
                // Formular desglose
                card.innerHTML = `
                    <div class="econ-card-title">${g.name}</div>
                    <div class="econ-card-sub">${g.member_count} alumnos · Rango seleccionado</div>

                    <div class="pie-chart-container">
                        <div class="pie-chart" style="background: conic-gradient(#22c55e 0deg ${angle}deg, #f04040 ${angle}deg 360deg)">
                            <div class="pie-chart-center">
                                <span class="pie-chart-center-value">${g.pct_ingreso}%</span>
                                <span class="pie-chart-center-label">Ingreso</span>
                            </div>
                        </div>
                        <div class="pie-chart-legend">
                            <div class="legend-item">
                                <span class="legend-dot" style="background:#22c55e"></span>
                                Ventas + Ingresos
                                <span class="legend-value">${fmt(g.ganancia_efectiva)}</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-dot" style="background:#f04040"></span>
                                Compras + Egresos
                                <span class="legend-value">${fmt(g.perdida_efectiva)}</span>
                            </div>
                            <div class="legend-item" style="margin-top:8px;padding-top:8px;border-top:1px solid var(--border-dim)">
                                <span class="legend-dot" style="background:var(--blue-neon)"></span>
                                Balance
                                ${balanceValHtml}
                            </div>
                        </div>
                    </div>

                    <div class="econ-summary">
                        <div class="econ-summary-item">
                            <span class="econ-summary-label">Ventas</span>
                            <span class="econ-summary-value positive">${fmt(g.ventas_total)}</span>
                        </div>
                        <div class="econ-summary-item">
                            <span class="econ-summary-label">Compras</span>
                            <span class="econ-summary-value negative">${fmt(g.compras_total)}</span>
                        </div>
                        <div class="econ-summary-item">
                            <span class="econ-summary-label">Otros ingresos</span>
                            <span class="econ-summary-value positive">${fmt(g.ingresos_total)}</span>
                        </div>
                        <div class="econ-summary-item">
                            <span class="econ-summary-label">Otros egresos</span>
                            <span class="econ-summary-value negative">${fmt(g.egresos_total)}</span>
                        </div>
                    </div>
                `;
                
                grid.appendChild(card);
            });

            if (skeleton) skeleton.style.display = 'none';
            if (grid) grid.style.display = 'grid';

        } catch (err) {
            console.error('[DesempenoEconomico]', err);
            if (skeleton) {
                skeleton.innerHTML = `
                    <div style="grid-column:1/-1;color:#f87171;font-size:.95rem;text-align:center;padding:32px">
                        ⚠️ Ocurrió un error al cargar los datos económicos. Por favor, reintentá.
                    </div>
                `;
            }
        }
    }

    // Inicializar cuando el módulo se vuelva visible por primera vez
    let loaded = false;
    function initOnVisible() {
        const section = document.querySelector('[data-module-content="desempeno-economico"]');
        if (!section) {
            init();
            load();
            return;
        }

        const obs = new MutationObserver(() => {
            if (section.classList.contains('active') && !loaded) {
                loaded = true;
                init();
                load();
            }
        });
        obs.observe(section, { attributes: true, attributeFilter: ['class'] });

        if (section.classList.contains('active')) {
            loaded = true;
            init();
            load();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOnVisible);
    } else {
        initOnVisible();
    }

    return { load };
})();
