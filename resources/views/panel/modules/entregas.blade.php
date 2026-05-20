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
<div class="modal-overlay" id="modalEntregaExpand" style="display:none" onclick="if(event.target===this) cerrarModalEntrega()">
    <div class="emodal-card">
        <button class="emodal-close" onclick="cerrarModalEntrega()">✕</button>
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
            <span>Producto</span><span>Precio</span>
        </div>
        <ul class="emodal-prods-list" id="emodProdsList"></ul>

        <div class="emodal-total">
            <span>Total</span>
            <span id="emodTotal">$0</span>
        </div>
        <div class="emodal-confirmado" id="emodConfirmado"></div>
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
    display:flex; align-items:center; justify-content:center; z-index:9999; animation:fadeInE .2s ease; }
@keyframes fadeInE{from{opacity:0}to{opacity:1}}

.emodal-card {
    background:#f5f0e8; border:1px solid #d9cdb8; border-radius:16px;
    padding:1.6rem; max-width:480px; width:92%; position:relative;
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
</style>

<script>
// ── Estado compartido con pedidos ──────────────────────
var entregas = [];
const PREVIEW_MAX = 5; // máximo de productos en preview

function renderEntregas() {
    const grid  = document.getElementById('entregasGrid');
    const empty = document.getElementById('entregasEmpty');
    const badge = document.getElementById('entregasBadge');
    if (!grid) return;

    badge.textContent = entregas.length + ' pendiente' + (entregas.length !== 1 ? 's' : '');
    grid.innerHTML = '';

    if (entregas.length === 0) { empty.style.display='flex'; return; }
    empty.style.display = 'none';

    entregas.forEach(e => {
        const total    = e.productos.reduce((s,x) => s + x.precio, 0);
        const preview  = e.productos.slice(0, PREVIEW_MAX);
        const hayMas   = e.productos.length > PREVIEW_MAX;
        const card     = document.createElement('div');
        card.className = 'entrega-card';
        card.dataset.id = e.id;

        const previewHTML = preview.map(p =>
            `<li class="ecard-producto-item">
                <span class="ecard-prod-nombre">${p.nombre}</span>
                <span class="ecard-prod-precio">$${p.precio.toLocaleString('es-AR')}</span>
             </li>`
        ).join('');

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
            </div>
            <hr class="ecard-divider">
            <ul class="ecard-productos">${previewHTML}</ul>
            ${verMasBtn}
            <div class="ecard-total">
                <span class="ecard-total-label">Total</span>
                <span class="ecard-total-valor">$${total.toLocaleString('es-AR')}</span>
            </div>
            <div class="ecard-confirmado">✅ Confirmado: ${e.confirmadoEn}</div>`;

        grid.appendChild(card);
    });
}

function expandirEntrega(id) {
    const e = entregas.find(x => x.id === id);
    if (!e) return;
    const total = e.productos.reduce((s,x) => s + x.precio, 0);

    document.getElementById('emodCliente').textContent    = '👤 ' + e.cliente;
    document.getElementById('emodNum').textContent        = '#' + e.id;
    document.getElementById('emodFecha').textContent      = e.fecha;
    document.getElementById('emodHora').textContent       = e.hora;
    document.getElementById('emodTotal').textContent      = '$' + total.toLocaleString('es-AR');
    document.getElementById('emodConfirmado').textContent = '✅ Confirmado: ' + e.confirmadoEn;

    const lista = document.getElementById('emodProdsList');
    lista.innerHTML = '';
    e.productos.forEach(p => {
        const li = document.createElement('li');
        li.innerHTML = `<span>${p.nombre}</span><span>$${p.precio.toLocaleString('es-AR')}</span>`;
        lista.appendChild(li);
    });

    document.getElementById('modalEntregaExpand').style.display = 'flex';
}

function cerrarModalEntrega() {
    document.getElementById('modalEntregaExpand').style.display = 'none';
}

// Render inicial
renderEntregas();
</script>
