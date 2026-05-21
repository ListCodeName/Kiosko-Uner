{{-- MÓDULO: ENTREGAS --}}
<div class="entregas-wrapper">

    {{-- ── ENCABEZADO ── --}}
    <div class="entregas-header">
        <h2 class="entregas-title">🚚 Entregas</h2>
        <span class="entregas-badge" id="entregasBadge">0 pendientes</span>
    </div>

    {{-- ── GRID DE TARJETAS ── --}}
    <div class="entregas-grid" id="entregasGrid"></div>

    {{-- ── ESTADO VACÍO ── --}}
    <div class="entregas-empty" id="entregasEmpty">
        <span>📭</span>
        <p>No hay entregas pendientes</p>
        <small>Los pedidos confirmados aparecerán aquí automáticamente</small>
    </div>

</div>

{{-- ══ MODAL EXPANDIR ENTREGA (crema) ══ --}}
<div class="modal-overlay" id="modalEntregaExpand" style="display:none" onclick="if(event.target===this) window.closeModal('modalEntregaExpand')">
    <div class="emodal-card">
        <button class="emodal-close modal-close" onclick="window.closeModal('modalEntregaExpand')">✕</button>
        <div class="emodal-stripe"></div>

        <div class="emodal-header">
            <span class="emodal-cliente" id="emodCliente">—</span>
            <span class="emodal-num" id="emodNum">#—</span>
        </div>
        <div class="emodal-meta">
            <span class="emodal-meta-item">📅 <strong id="emodFecha">—</strong></span>
            <span class="emodal-meta-item">🕐 <strong id="emodHora">—</strong></span>
        </div>

        <hr class="emodal-divider">

        <div class="emodal-prods-header">
            <span>Producto</span><span>Subtotal</span>
        </div>
        <ul class="emodal-prods-list" id="emodProdsList"></ul>

        <div class="emodal-total">
            <span>Total</span>
            <span id="emodTotal">$0</span>
        </div>
        <div class="emodal-confirmado" id="emodConfirmado"></div>
        
        <button class="btn btn-confirm" style="width: 100%; justify-content: center; margin-top: 1rem; padding: .6rem; background: linear-gradient(135deg,#c8a96e,#a07840); border: 1px solid #7a5a30; color:#3d2e1a; font-weight:bold;" onclick="abrirModalEntregarDesdeExpand()">
            🚚 Entregar y Cobrar
        </button>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: ENTREGAR PEDIDO
     Permite seleccionar el método de pago antes de cobrar.
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalEntregar" style="display:none" onclick="if(event.target===this) window.closeModal('modalEntregar')">
    <div class="modal-card">
        <div class="modal-icon">🚚</div>
        <h3 class="modal-title">Entregar y Cobrar Pedido</h3>
        <p class="modal-desc" style="margin-bottom: 1rem;">Confirmá la entrega del pedido y seleccioná el método de pago utilizado.</p>
        
        <div class="form-group" style="margin-bottom: 1.4rem;">
            <label>Método de Pago</label>
            <select id="entregasMetodoPago" class="form-input">
                <option value="efectivo">💵 Efectivo</option>
                <option value="transferencia">📱 Transferencia</option>
            </select>
        </div>

        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="window.closeModal('modalEntregar')">Cancelar</button>
            <button class="btn btn-confirm" id="btnConfirmarEntrega" onclick="procesarEntrega()">🚚 Entregar y Registrar</button>
        </div>
    </div>
</div>

<style>
/* ── Wrapper ── */
.entregas-wrapper { display:flex; flex-direction:column; gap:1.2rem; }

/* ── Encabezado ── */
.entregas-header {
    display:flex; align-items:center; gap:1rem;
    background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);
    border-radius:12px; padding:.9rem 1.2rem;
}
.entregas-title { margin:0; font-size:1.2rem; font-weight:600; color:#e2e8f0; }
.entregas-badge {
    background:rgba(251,191,36,.15); color:#fbbf24;
    border:1px solid rgba(251,191,36,.3);
    border-radius:20px; padding:.2rem .8rem;
    font-size:.78rem; font-weight:700; letter-spacing:.03em;
}

/* ── Grid 5 columnas ── */
.entregas-grid {
    display:grid; grid-template-columns:repeat(5,1fr); gap:1rem;
}
@media(max-width:1400px){.entregas-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:1100px){.entregas-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:760px) {.entregas-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px) {.entregas-grid{grid-template-columns:1fr}}

/* ── Tarjeta crema ── */
.entrega-card {
    background:#f5f0e8; border:1px solid #d9cdb8; border-radius:12px;
    padding:1rem; display:flex; flex-direction:column; gap:.6rem;
    box-shadow:0 2px 10px rgba(0,0,0,.18); animation:cardIn .3s ease;
    position:relative; overflow:hidden;
}
.entrega-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:4px;
    background:linear-gradient(90deg,#c8a96e,#e6c88a);
}
@keyframes cardIn {
    from{opacity:0;transform:translateY(12px) scale(.97)}
    to  {opacity:1;transform:translateY(0)    scale(1)}
}

/* ── Header tarjeta ── */
.ecard-header { display:flex; align-items:flex-start; justify-content:space-between; gap:.4rem; }
.ecard-cliente { font-size:.9rem; font-weight:700; color:#3d2e1a; line-height:1.2; word-break:break-word; }
.ecard-num { font-size:.72rem; font-weight:700; color:#a07840; background:#e8dcc8; border-radius:6px; padding:.15rem .4rem; flex-shrink:0; }

/* ── Meta ── */
.ecard-meta { display:flex; gap:.5rem; flex-wrap:wrap; }
.ecard-meta-item { display:flex; align-items:center; gap:.25rem; font-size:.72rem; color:#7a6040; background:#ece4d4; border-radius:6px; padding:.15rem .45rem; }

/* ── Divisor ── */
.ecard-divider { border:none; border-top:1px solid #d0c4af; margin:.1rem 0; }

/* ── Productos en tarjeta (preview) ── */
.ecard-productos { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.3rem; }
.ecard-producto-item { display:flex; justify-content:space-between; align-items:center; font-size:.75rem; color:#4a3820; }
.ecard-prod-nombre { flex:1; }
.ecard-prod-precio { font-weight:700; color:#8b5e2a; white-space:nowrap; }

/* ── Botón "ver más" ── */
.ecard-ver-mas {
    display:inline-flex; align-items:center; justify-content:center; gap:.3rem;
    width:100%; margin-top:.2rem; padding:.35rem; border-radius:8px;
    background:#e0d4bc; color:#7a5a30; font-size:.74rem; font-weight:700;
    border:1px dashed #c8a96e; cursor:pointer; transition:all .18s;
}
.ecard-ver-mas:hover { background:#d0c0a0; color:#5a3e20; }

/* ── Total ── */
.ecard-total { display:flex; justify-content:space-between; align-items:center; background:#e0d4bc; border-radius:8px; padding:.4rem .6rem; margin-top:.2rem; }
.ecard-total-label { font-size:.75rem; font-weight:700; color:#5a3e20; }
.ecard-total-valor { font-size:.9rem; font-weight:800; color:#6b3e10; }

/* ── Confirmado ── */
.ecard-confirmado { font-size:.68rem; color:#9a8060; text-align:right; margin-top:.1rem; }

/* ── Estado vacío ── */
.entregas-empty { display:flex; flex-direction:column; align-items:center; gap:.4rem; padding:4rem 1rem; color:#64748b; text-align:center; }
.entregas-empty span { font-size:3rem; }
.entregas-empty p { margin:0; font-size:.95rem; color:#94a3b8; }
.entregas-empty small { color:#475569; font-size:.8rem; }

/* ══ Modal expandir entrega (crema) ══ */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(6px);
    display:none; align-items:center; justify-content:center; z-index:9999; animation:fadeInE .2s ease; }
@keyframes fadeInE{from{opacity:0}to{opacity:1}}

.emodal-card {
    background:#f5f0e8; border:1px solid #d9cdb8; border-radius:16px;
    padding:1.6rem; max-width: min(560px, 90dvw); width:92%; position:relative;
    box-shadow:0 8px 32px rgba(0,0,0,.35);
    animation:slideUpE .25s ease;
}
@keyframes slideUpE{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}

.emodal-stripe {
    position:absolute; top:0; left:0; right:0; height:5px;
    background:linear-gradient(90deg,#c8a96e,#e6c88a); border-radius:16px 16px 0 0;
}
.emodal-close {
    position:absolute; top:.8rem; right:.8rem;
    background:#e0d4bc; border:none; color:#7a5a30;
    border-radius:8px; width:28px; height:28px; cursor:pointer; font-size:.9rem; font-weight:700;
}
.emodal-close:hover { background:#d0c0a0; }

.emodal-header { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-top:.4rem; margin-bottom:.5rem; }
.emodal-cliente { font-size:1.05rem; font-weight:800; color:#3d2e1a; }
.emodal-num { font-size:.78rem; font-weight:700; color:#a07840; background:#e8dcc8; border-radius:6px; padding:.2rem .5rem; flex-shrink:0; }

.emodal-meta { display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:.6rem; }
.emodal-meta-item { font-size:.8rem; color:#7a6040; background:#ece4d4; border-radius:6px; padding:.2rem .5rem; }
.emodal-meta-item strong { color:#4a3820; }

.emodal-divider { border:none; border-top:1px solid #d0c4af; margin:.4rem 0; }

.emodal-prods-header { display:flex; justify-content:space-between; font-size:.72rem; color:#9a7a50; text-transform:uppercase; letter-spacing:.04em; padding:0 .3rem .4rem; border-bottom:1px solid #d0c4af; }

.emodal-prods-list {
    list-style:none; padding:0; margin:.4rem 0;
    max-height:280px; overflow-y:auto;
}
.emodal-prods-list::-webkit-scrollbar { width:5px; }
.emodal-prods-list::-webkit-scrollbar-track { background:#ece4d4; border-radius:4px; }
.emodal-prods-list::-webkit-scrollbar-thumb { background:#c8a96e; border-radius:4px; }
.emodal-prods-list::-webkit-scrollbar-thumb:hover { background:#a07840; }
.emodal-prods-list li { display:flex; justify-content:space-between; padding:.5rem .3rem; border-bottom:1px solid #e0d4bc; font-size:.84rem; color:#4a3820; }
.emodal-prods-list li:last-child { border-bottom:none; }
.emodal-prods-list li span:last-child { font-weight:700; color:#8b5e2a; }

.emodal-total { display:flex; justify-content:space-between; padding:.7rem .3rem 0; border-top:2px solid #c8a96e; font-weight:800; color:#3d2e1a; font-size:1rem; margin-top:.4rem; }
.emodal-total span:last-child { color:#6b3e10; }

.emodal-confirmado { font-size:.7rem; color:#9a8060; text-align:right; margin-top:.5rem; }

/* ── Estilos inputs select premium ── */
select.form-input {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .8rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}
</style>

<script>
// ── Estado ──────────────────────────────────────────────
let entregasLocal = [];
let entregaAProcesar = null;
const PREVIEW_MAX = 5;

// ── Cargar entregas del servidor (pedidos con estado 'confirmed') ──
window.refreshEntregas = async function() {
    try {
        const res = await fetch('/panel/api/pedidos');
        const data = await res.json();
        entregasLocal = data.filter(p => p.estado === 'confirmed');
        renderEntregas();
    } catch(e) {
        console.warn('Error al cargar entregas desde el servidor:', e);
    }
};

// ── Render de entregas ───────────────────────────────────
function renderEntregas() {
    const grid  = document.getElementById('entregasGrid');
    const empty = document.getElementById('entregasEmpty');
    const badge = document.getElementById('entregasBadge');
    if (!grid) return;

    if (badge) {
        badge.textContent = entregasLocal.length + ' pendiente' + (entregasLocal.length !== 1 ? 's' : '');
    }
    grid.innerHTML = '';

    if (entregasLocal.length === 0) { empty.style.display='flex'; return; }
    empty.style.display = 'none';

    entregasLocal.forEach(e => {
        const preview  = e.productos.slice(0, PREVIEW_MAX);
        const hayMas   = e.productos.length > PREVIEW_MAX;
        const card     = document.createElement('div');
        card.className = 'entrega-card';
        card.dataset.id = e.id;

        const previewHTML = preview.map(p => {
            const qtyLabel = p.cantidad > 1 ? ` x${p.cantidad}` : '';
            return `<li class="ecard-producto-item">
                <span class="ecard-prod-nombre">${p.nombre}${qtyLabel}</span>
                <span class="ecard-prod-precio">$${p.subtotal.toLocaleString('es-AR')}</span>
             </li>`;
        }).join('');

        const verMasBtn = hayMas
            ? `<button class="ecard-ver-mas" onclick="expandirEntrega(${e.id})">
                   📋 Ver los ${e.productos.length} productos
               </button>`
            : '';

        card.innerHTML = `
            <div class="ecard-header">
                <span class="ecard-cliente">👤 ${e.cliente}</span>
                <span class="ecard-num">#${e.id}</span>
            </div>
            <div class="ecard-meta">
                <span class="ecard-meta-item">📅 ${e.fecha}</span>
                <span class="ecard-meta-item">🕐 ${e.hora}</span>
                ${e.horaEntrega ? `<span class="ecard-meta-item" style="background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.25); font-weight: bold;">🕐 Est: ${e.horaEntrega}</span>` : ''}
            </div>
            <hr class="ecard-divider">
            <ul class="ecard-productos">${previewHTML}</ul>
            ${verMasBtn}
            <div class="ecard-total">
                <span class="ecard-total-label">Total</span>
                <span class="ecard-total-valor">$${e.total.toLocaleString('es-AR')}</span>
            </div>
            <button class="btn btn-confirm" style="width: 100%; justify-content: center; margin-top: .6rem; padding: .45rem; background: linear-gradient(135deg, #c8a96e, #a07840); color: #3d2e1a; font-weight: 700; border: 1px solid #7a5a30; box-shadow: 0 4px 10px rgba(160,120,64,0.25);" onclick="abrirModalEntregar(${e.id})">
                🚚 Entregar
            </button>`;

        grid.appendChild(card);
    });
}

// ── Expandir ─────────────────────────────────────────────
function expandirEntrega(id) {
    const e = entregasLocal.find(x => x.id === id);
    if (!e) return;

    document.getElementById('emodCliente').textContent    = '👤 ' + e.cliente;
    document.getElementById('emodNum').textContent        = '#' + e.id;
    document.getElementById('emodFecha').textContent      = e.fecha;
    document.getElementById('emodHora').textContent       = e.hora;
    document.getElementById('emodTotal').textContent      = '$' + e.total.toLocaleString('es-AR');
    document.getElementById('emodConfirmado').textContent = e.horaEntrega ? '🕐 Entrega estimada: ' + e.horaEntrega : '🕐 Sin horario definido';

    const lista = document.getElementById('emodProdsList');
    if (lista) {
        lista.innerHTML = '';
        e.productos.forEach(p => {
            const li = document.createElement('li');
            const qtyLabel = p.cantidad > 1 ? ` x${p.cantidad}` : '';
            li.innerHTML = `<span>${p.nombre}${qtyLabel}</span><span>$${p.subtotal.toLocaleString('es-AR')}</span>`;
            lista.appendChild(li);
        });
    }

    window.openModal('modalEntregaExpand');
}

// ── Entregar y Cobrar ────────────────────────────────────
function abrirModalEntregar(id) {
    entregaAProcesar = id;
    window.openModal('modalEntregar');
}

function abrirModalEntregarDesdeExpand() {
    const numSpan = document.getElementById('emodNum');
    if (!numSpan) return;
    const id = parseInt(numSpan.textContent.replace('#', ''));
    if (!id) return;
    abrirModalEntregar(id);
}

function procesarEntrega() {
    const id = entregaAProcesar;
    if (!id) return;
    const metodoPago = document.getElementById('entregasMetodoPago').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/panel/api/pedidos/${id}/entregar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ metodo_pago: metodoPago })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.closeModal('modalEntregar');
            window.closeModal('modalEntregaExpand');
            window.refreshEntregas();
            
            // Refrescar módulo de ventas en tiempo real
            if (typeof window.VentasModuleRefresh === 'function') {
                window.VentasModuleRefresh();
            }

            // Refrescar módulo de pedidos en tiempo real
            if (typeof window.refreshPedidos === 'function') {
                window.refreshPedidos();
            }
        } else {
            alert(data.message || 'Error al procesar la entrega.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al registrar la entrega.');
    });
    entregaAProcesar = null;
}

// Carga Inicial
window.refreshEntregas();
setInterval(window.refreshEntregas, 60000);
</script>
