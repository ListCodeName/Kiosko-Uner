{{-- MÓDULO: VENTAS --}}

{{-- ══ TOOLBAR ══ --}}
<div class="vt-toolbar">
    <div class="vt-summary">
        <span class="vt-badge" id="vtTotalCount">0 ventas</span>
        <span class="vt-badge vt-badge--green" id="vtTotalMonto">$0,00 total</span>
        <span class="vt-badge vt-badge--yellow" id="vtPendienteCount">0 pendientes</span>
    </div>

    <div class="vt-filters">
        <select class="vt-select" id="vtFilterEstado">
            <option value="">Todas las ventas</option>
            <option value="pagado">Pagadas</option>
            <option value="pendiente">Pendientes</option>
        </select>

        <select class="vt-select" id="vtFilterMetodo">
            <option value="">Todos los métodos</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
        </select>

        <div class="vt-search-wrap">
            <span class="vt-search-icon">📅</span>
            <input type="date" class="vt-search-date" id="vtFilterFecha" title="Filtrar por fecha">
        </div>

        {{-- Limpiar filtros --}}
        <button class="vt-clear-filters" id="vtClearFilters" title="Limpiar filtros">✕ Limpiar</button>
    </div>
</div>

{{-- ══ GRID DE TARJETAS ══ --}}
<div class="vt-grid" id="vtGrid"></div>

{{-- ══ EMPTY STATE ══ --}}
<div class="vt-empty" id="vtEmpty" style="display:none">
    <div class="empty-state" style="min-height:320px">
        <div class="empty-state-illustration">
            <div class="empty-state-pulse"></div>
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="10" y="12" width="36" height="32" rx="3" stroke="#2d8cff" stroke-width="2" opacity="0.8"/>
                <line x1="10" y1="22" x2="46" y2="22" stroke="#2d8cff" stroke-width="1.5" opacity="0.4"/>
                <line x1="18" y1="30" x2="38" y2="30" stroke="#6ab4ff" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>
                <line x1="18" y1="36" x2="30" y2="36" stroke="#6ab4ff" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                <circle cx="42" cy="40" r="8" fill="#0d0f14" stroke="#2d8cff" stroke-width="1.5" opacity="0.9"/>
                <text x="39" y="44" font-size="9" fill="#6ab4ff" font-family="Inter">$</text>
            </svg>
        </div>
        <h2 class="empty-state-title">Sin ventas registradas</h2>
        <p class="empty-state-desc">
            No hay ventas que coincidan con los filtros.<br>
            Registrá ventas desde el módulo <strong>Kiosco</strong>.
        </p>
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="vtToast"></div>

{{-- ══════════════════════════════════════════════════════
     MODAL: CONFIRMAR PAGO (Efectivizar venta pendiente)
══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="vtPayModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header" style="border-bottom-color:rgba(251,191,36,.2)">
            <h3 class="modal-title">💰 Confirmar Pago</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="vtPayForm" style="gap:16px">
            <input type="hidden" id="vt-pay-id">
            <p style="color:var(--text-secondary);font-size:.88rem">
                ¿Confirmás que se recibió el pago para esta venta?
            </p>
            <div id="vtPayPreview" class="vt-preview-box"></div>
            <div class="form-group">
                <label class="form-label">Método de pago recibido</label>
                <select class="form-input" id="vt-pay-metodo">
                    <option value="efectivo">💵 Efectivo</option>
                    <option value="transferencia">📲 Transferencia</option>
                </select>
            </div>
            <div class="modal-footer" style="margin-top:8px">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit" style="background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 0 16px rgba(245,158,11,.3)">
                    ✅ Confirmar Pago
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: DEVOLVER VENTA
══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="vtReturnModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header" style="border-bottom-color:rgba(168,85,247,.25)">
            <h3 class="modal-title">↩️ Devolver Venta</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="vtReturnForm" style="gap:14px">
            <input type="hidden" id="vt-return-id">
            <p style="color:var(--text-secondary);font-size:.88rem">
                ¿Confirmás la devolución de esta venta? El stock de los productos será <strong style="color:var(--text-primary)">restablecido</strong>.
            </p>
            <div id="vtReturnPreview" class="vt-preview-box"></div>
            <p style="color:var(--text-muted);font-size:.78rem;background:rgba(168,85,247,.06);border:1px solid rgba(168,85,247,.15);border-radius:8px;padding:.6rem .8rem;">
                ⚠️ Esta acción eliminará el registro de venta y devolverá las unidades al inventario.
            </p>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" style="padding:9px 20px;background:linear-gradient(135deg,rgba(168,85,247,.2),rgba(168,85,247,.1));border:1px solid rgba(168,85,247,.4);border-radius:6px;color:#c084fc;font-family:inherit;font-size:.85rem;font-weight:600;cursor:pointer;transition:background .18s;">
                    ↩️ Confirmar Devolución
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ESTILOS DEL MÓDULO
══════════════════════════════════════════════════════ --}}
<style>
/* ── Toolbar ── */
.vt-toolbar {
    display: flex; align-items: center; flex-wrap: wrap;
    gap: .85rem;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 12px; padding: .9rem 1.1rem;
}
.vt-summary { display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
.vt-badge {
    display: inline-flex; align-items: center;
    padding: .22rem .75rem; border-radius: 20px;
    font-size: .75rem; font-weight: 700; letter-spacing: .03em;
    background: rgba(45,140,255,.12); border: 1px solid rgba(45,140,255,.25);
    color: var(--blue-light); white-space: nowrap;
}
.vt-badge--green   { background: rgba(34,197,94,.12);  border-color: rgba(34,197,94,.25);  color: #4ade80; }
.vt-badge--yellow  { background: rgba(251,191,36,.12); border-color: rgba(251,191,36,.3);  color: #fbbf24; }

.vt-filters { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; flex: 1; justify-content: flex-end; }

.vt-select {
    background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px; color: var(--text-primary); font-family: inherit;
    font-size: .82rem; padding: .45rem .7rem;
    outline: none; cursor: pointer; transition: border-color .18s;
}
.vt-select:focus { border-color: var(--blue-neon); }

.vt-search-wrap {
    display: flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px; padding: .35rem .65rem; transition: border-color .18s;
}
.vt-search-wrap:focus-within { border-color: var(--blue-neon); }
.vt-search-icon { font-size: .9rem; flex-shrink: 0; }
.vt-search-date {
    background: none; border: none; color: var(--text-primary);
    font-family: inherit; font-size: .82rem; outline: none; width: 140px;
}
.vt-search-date::-webkit-calendar-picker-indicator { filter: invert(.6); cursor: pointer; }

.vt-clear-filters {
    background: none; border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px; color: var(--text-muted); font-family: inherit;
    font-size: .78rem; padding: .4rem .7rem; cursor: pointer;
    transition: background .15s, color .15s;
}
.vt-clear-filters:hover { background: rgba(255,255,255,.06); color: var(--text-secondary); }

/* ── Grid ── */
.vt-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

/* ── Tarjeta ── */
.vt-card {
    background: var(--bg-card); border: 1px solid var(--border-dim);
    border-radius: 14px; overflow: hidden; display: flex; flex-direction: column;
    transition: border-color .2s, box-shadow .2s, transform .2s; position: relative;
}
.vt-card:hover { border-color: var(--border-mid); box-shadow: 0 6px 28px rgba(0,0,0,.4); transform: translateY(-2px); }

.vt-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.vt-card--pagado::before   { background: linear-gradient(90deg,#22c55e,#4ade80); }
.vt-card--pendiente::before { background: linear-gradient(90deg,#f59e0b,#fbbf24); }

/* Header tarjeta */
.vt-card-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 1rem 1rem .6rem; gap: .5rem;
}
.vt-card-num {
    font-size: .72rem; font-weight: 700; color: var(--blue-light);
    background: rgba(45,140,255,.12); border-radius: 6px; padding: .18rem .5rem; flex-shrink: 0;
}
.vt-card-actions { display: flex; gap: .35rem; }
.vt-card-act-btn {
    background: none; border: 1px solid rgba(255,255,255,.08);
    border-radius: 7px; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; cursor: pointer; color: var(--text-muted);
    transition: background .15s, border-color .15s, color .15s;
}
.vt-card-act-btn:hover      { background: rgba(255,255,255,.08); color: var(--text-primary); border-color: rgba(255,255,255,.18); }
.vt-card-act-btn--pay:hover    { background: rgba(251,191,36,.15); border-color: rgba(251,191,36,.4); color: #fbbf24; }
.vt-card-act-btn--return:hover { background: rgba(168,85,247,.12); border-color: rgba(168,85,247,.4); color: #c084fc; }

/* Cuerpo */
.vt-card-body { padding: 0 1rem .75rem; flex: 1; }
.vt-card-cliente {
    font-size: .9rem; font-weight: 600; color: var(--text-white);
    margin-bottom: .45rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.vt-card-meta { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: .55rem; }
.vt-card-meta-item {
    display: inline-flex; align-items: center; gap: .25rem; font-size: .72rem;
    color: var(--text-secondary); background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.06); border-radius: 6px; padding: .15rem .45rem;
}

/* Badge estado */
.vt-status-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    font-size: .73rem; font-weight: 700; border-radius: 20px; padding: .2rem .65rem; border: 1px solid;
}
.vt-status-badge--pagado   { background: rgba(34,197,94,.1);  border-color: rgba(34,197,94,.3);  color: #4ade80; }
.vt-status-badge--pendiente { background: rgba(251,191,36,.1); border-color: rgba(251,191,36,.3); color: #fbbf24; }

/* Total */
.vt-card-total {
    display: flex; justify-content: space-between; align-items: center;
    background: rgba(45,140,255,.06); border: 1px solid rgba(45,140,255,.12);
    border-radius: 8px; padding: .45rem .7rem; margin: 0 1rem .75rem;
}
.vt-card-total-label  { font-size: .75rem; color: var(--text-secondary); font-weight: 600; }
.vt-card-total-amount { font-size: 1.05rem; font-weight: 800; color: var(--blue-light); }
.vt-card--pendiente .vt-card-total { background: rgba(251,191,36,.06); border-color: rgba(251,191,36,.14); }
.vt-card--pendiente .vt-card-total-amount { color: #fbbf24; }

/* Acordeón */
.vt-card-accordion { border-top: 1px solid rgba(255,255,255,.05); }
.vt-accordion-toggle {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; padding: .6rem 1rem;
    background: none; border: none; cursor: pointer;
    color: var(--text-secondary); font-size: .78rem; font-weight: 600;
    font-family: inherit; transition: color .15s, background .15s;
}
.vt-accordion-toggle:hover { background: rgba(255,255,255,.03); color: var(--text-primary); }
.vt-accordion-arrow { font-size: .65rem; transition: transform .22s ease; color: var(--text-muted); }
.vt-accordion-toggle.open .vt-accordion-arrow { transform: rotate(90deg); }

.vt-accordion-body { max-height: 0; overflow: hidden; transition: max-height .28s ease; }
.vt-accordion-body.open { max-height: 600px; }

.vt-prod-list { list-style: none; padding: 0 1rem .75rem; margin: 0; display: flex; flex-direction: column; gap: .2rem; }
.vt-prod-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: .38rem .4rem; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .78rem;
}
.vt-prod-item:last-child { border-bottom: none; }
.vt-prod-name  { color: var(--text-primary); flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.vt-prod-qty   { color: var(--text-muted); width: 36px; text-align: center; font-size: .72rem; }
.vt-prod-sub   { color: var(--blue-light); font-weight: 700; white-space: nowrap; }
.vt-card--pendiente .vt-prod-sub { color: #fbbf24; }

/* Preview en modales */
.vt-preview-box {
    background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.07);
    border-radius: 10px; padding: .75rem 1rem; display: flex; flex-direction: column; gap: .45rem;
}
.vt-preview-row { display: flex; justify-content: space-between; font-size: .82rem; color: var(--text-secondary); }
.vt-preview-row span:last-child { color: var(--text-white); font-weight: 600; }

/* Indicador pulsante para pendientes */
.vt-pending-dot {
    display: inline-block; width: 7px; height: 7px; border-radius: 50%;
    background: #f59e0b; margin-right: 4px;
    animation: vtDotPulse 1.6s ease-in-out infinite;
}
@keyframes vtDotPulse {
    0%,100% { opacity: 1; box-shadow: 0 0 0 0 rgba(245,158,11,.5); }
    50%      { opacity: .7; box-shadow: 0 0 0 5px rgba(245,158,11,0); }
}

/* Empty */
.vt-empty { display: none; }

@media(max-width:640px) {
    .vt-grid { grid-template-columns: 1fr; }
    .vt-filters { justify-content: flex-start; }
}
</style>

{{-- ══════════════════════════════════════════════════════
     JAVASCRIPT DEL MÓDULO VENTAS
══════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    /* ── Clave compartida con kiosco.js ── */
    const STORAGE_KEY = 'kiosko_ventas';

    let ventasData = [];
    let loaded     = false;

    /* Filtros */
    let fEstado = '';
    let fMetodo = '';
    let fFecha  = '';

    /* ── Persistencia ── */
    function storageLoad() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            ventasData = raw ? JSON.parse(raw) : [];
        } catch (e) { ventasData = []; }
    }

    function storageSave() {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(ventasData)); } catch (e) {}
    }

    /* ── Helpers ── */
    function formatMoney(v) {
        return '$' + Number(v).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function formatFecha(str) {
        if (!str) return '—';
        const [y, m, d] = str.split('-');
        return `${d}/${m}/${y}`;
    }
    function metodoLabel(m) {
        return m === 'efectivo' ? '💵 Efectivo' : '📲 Transferencia';
    }
    function toast(msg, type = 'success') {
        const t = document.getElementById('vtToast');
        if (!t) return;
        t.textContent = msg;
        t.className = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ── Filtros ── */
    function filtered() {
        return ventasData.filter(v => {
            if (fEstado && v.estado  !== fEstado) return false;
            if (fMetodo && v.metodo  !== fMetodo) return false;
            if (fFecha  && v.fecha   !== fFecha)  return false;
            return true;
        });
    }

    /* ── Render ── */
    function render() {
        const grid  = document.getElementById('vtGrid');
        const empty = document.getElementById('vtEmpty');

        // Resumen total (sin filtrar)
        const montoTotal = ventasData.reduce((s, v) => s + v.total, 0);
        const pendCount  = ventasData.filter(v => v.estado === 'pendiente').length;
        const countEl = document.getElementById('vtTotalCount');
        const montoEl = document.getElementById('vtTotalMonto');
        const pendEl  = document.getElementById('vtPendienteCount');
        if (countEl) countEl.textContent = `${ventasData.length} venta${ventasData.length !== 1 ? 's' : ''}`;
        if (montoEl) montoEl.textContent = formatMoney(montoTotal) + ' total';
        if (pendEl)  pendEl.textContent  = `${pendCount} pendiente${pendCount !== 1 ? 's' : ''}`;

        const rows = filtered();
        if (!grid) return;

        if (!rows.length) {
            grid.innerHTML = '';
            if (empty) empty.style.display = 'flex';
            return;
        }
        if (empty) empty.style.display = 'none';

        grid.innerHTML = rows.map(v => buildCard(v)).join('');

        // Bind acordeones
        grid.querySelectorAll('.vt-accordion-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.toggle('open');
                btn.nextElementSibling?.classList.toggle('open');
            });
        });
    }

    function buildCard(v) {
        const isPend    = v.estado === 'pendiente';
        const cardClass = isPend ? 'vt-card--pendiente' : 'vt-card--pagado';

        const statusBadge = isPend
            ? `<span class="vt-status-badge vt-status-badge--pendiente"><span class="vt-pending-dot"></span>Pendiente</span>`
            : `<span class="vt-status-badge vt-status-badge--pagado">✅ Pagado</span>`;

        /* Botón pagar: solo para pendientes */
        const payBtn = isPend
            ? `<button class="vt-card-act-btn vt-card-act-btn--pay" title="Efectivizar pago" onclick="VT.pay(${v.id})">💰</button>`
            : '';

        const productosList = (v.items || []).map(i => `
            <li class="vt-prod-item">
                <span class="vt-prod-name">${i.nombre}</span>
                <span class="vt-prod-qty">×${i.cantidad}</span>
                <span class="vt-prod-sub">${formatMoney(i.cantidad * i.precio)}</span>
            </li>`).join('');

        return `
        <div class="vt-card ${cardClass}" id="vtCard-${v.id}">
            <div class="vt-card-header">
                <span class="vt-card-num">#${String(v.id).padStart(4,'0')}</span>
                <div style="flex:1;min-width:0;padding:0 .4rem">${statusBadge}</div>
                <div class="vt-card-actions">
                    ${payBtn}
                    <button class="vt-card-act-btn vt-card-act-btn--return" title="Devolver venta" onclick="VT.devolver(${v.id})">↩️</button>
                </div>
            </div>

            <div class="vt-card-body">
                <div class="vt-card-cliente">${v.cliente || 'Venta general'}</div>
                <div class="vt-card-meta">
                    <span class="vt-card-meta-item">📅 ${formatFecha(v.fecha)}</span>
                    <span class="vt-card-meta-item">🕐 ${v.hora}</span>
                    <span class="vt-card-meta-item">${metodoLabel(v.metodo)}</span>
                </div>
                ${v.obs ? `<div class="vt-card-meta-item" style="font-size:.74rem;color:var(--text-muted);margin-top:.2rem;max-width:100%;white-space:normal">${v.obs}</div>` : ''}
            </div>

            <div class="vt-card-total">
                <span class="vt-card-total-label">Total vendido</span>
                <span class="vt-card-total-amount">${formatMoney(v.total)}</span>
            </div>

            <div class="vt-card-accordion">
                <button class="vt-accordion-toggle" type="button">
                    <span>Ver ${(v.items || []).length} producto${(v.items||[]).length !== 1 ? 's' : ''}</span>
                    <span class="vt-accordion-arrow">▶</span>
                </button>
                <div class="vt-accordion-body">
                    <ul class="vt-prod-list">${productosList}</ul>
                </div>
            </div>
        </div>`;
    }

    /* ── Efectivizar pago ── */
    window.VT = window.VT || {};

    VT.pay = function (id) {
        const v = ventasData.find(x => x.id === id);
        if (!v) return;
        document.getElementById('vt-pay-id').value     = id;
        document.getElementById('vt-pay-metodo').value = v.metodo;
        document.getElementById('vtPayPreview').innerHTML = `
            <div class="vt-preview-row"><span>N° de venta</span><span>#${String(v.id).padStart(4,'0')}</span></div>
            <div class="vt-preview-row"><span>Cliente</span><span>${v.cliente || 'Venta general'}</span></div>
            <div class="vt-preview-row"><span>Total a cobrar</span><span style="color:#fbbf24;font-size:1.05rem">${formatMoney(v.total)}</span></div>
            <div class="vt-preview-row"><span>Fecha / Hora</span><span>${formatFecha(v.fecha)} – ${v.hora}</span></div>`;
        window.openModal('vtPayModal');
    };

    document.getElementById('vtPayForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const id     = parseInt(document.getElementById('vt-pay-id').value);
        const metodo = document.getElementById('vt-pay-metodo').value;
        const idx    = ventasData.findIndex(x => x.id === id);
        if (idx !== -1) {
            ventasData[idx].estado = 'pagado';
            ventasData[idx].metodo = metodo;
        }
        storageSave();
        window.closeModal('vtPayModal');
        render();
        toast('✅ Pago confirmado exitosamente');
    });

    /* ── Devolver venta ── */
    VT.devolver = function (id) {
        const v = ventasData.find(x => x.id === id);
        if (!v) return;
        document.getElementById('vt-return-id').value = id;
        document.getElementById('vtReturnPreview').innerHTML = `
            <div class="vt-preview-row"><span>N° de venta</span><span>#${String(v.id).padStart(4,'0')}</span></div>
            <div class="vt-preview-row"><span>Cliente</span><span>${v.cliente || 'Venta general'}</span></div>
            <div class="vt-preview-row"><span>Total</span><span style="color:#c084fc">${formatMoney(v.total)}</span></div>
            <div class="vt-preview-row"><span>Productos</span><span>${(v.items||[]).length} ítems a reponer</span></div>`;
        window.openModal('vtReturnModal');
    };

    document.getElementById('vtReturnForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const id  = parseInt(document.getElementById('vt-return-id').value);
        const v   = ventasData.find(x => x.id === id);

        if (v) {
            /* Reintegrar stock en kiosco si la función está disponible */
            if (typeof window.KioscoRestoreStock === 'function') {
                window.KioscoRestoreStock(v.items);
            }
            ventasData = ventasData.filter(x => x.id !== id);
            storageSave();
        }

        window.closeModal('vtReturnModal');
        render();
        toast('↩️ Venta devuelta — stock restablecido');
    });

    /* ── Filtros ── */
    document.getElementById('vtFilterEstado')?.addEventListener('change', function () { fEstado = this.value; render(); });
    document.getElementById('vtFilterMetodo')?.addEventListener('change', function () { fMetodo = this.value; render(); });
    document.getElementById('vtFilterFecha')?.addEventListener('change',  function () { fFecha  = this.value; render(); });
    document.getElementById('vtClearFilters')?.addEventListener('click', function () {
        fEstado = ''; fMetodo = ''; fFecha = '';
        document.getElementById('vtFilterEstado').value = '';
        document.getElementById('vtFilterMetodo').value = '';
        document.getElementById('vtFilterFecha').value  = '';
        render();
    });

    /* ── Carga inicial con MutationObserver ── */
    function load() {
        if (loaded) return;
        storageLoad();
        loaded = true;
        render();
    }

    /* Refrescar al volver al módulo (por si se registraron ventas en kiosco) */
    const section = document.querySelector('[data-module-content="ventas"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) {
                storageLoad(); // siempre releer al activar
                loaded = true;
                render();
            }
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
        if (section.classList.contains('active')) load();
    }

    /* API pública para que kiosco.js notifique nuevas ventas sin recargar la página */
    window.VentasModuleRefresh = function () {
        storageLoad();
        render();
    };

})();
</script>
