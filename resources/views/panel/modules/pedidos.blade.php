{{-- MÓDULO: PEDIDOS --}}
<div class="pedidos-wrapper">

    {{-- ── BARRA DE ACCIONES ── --}}
    <div class="pedidos-toolbar">
        <div class="pedidos-toolbar-left">
            <h2 class="pedidos-title">📋 Pedidos</h2>
        </div>
        <div class="pedidos-toolbar-actions">
            <button class="btn btn-add" id="btnAnadirPedido" onclick="abrirModalAnadir()">
                <span>＋</span> Nuevo Pedido
            </button>
        </div>
    </div>

    {{-- ── TABLA DE PEDIDOS ── --}}
    <div class="pedidos-table-container">
        <table class="pedidos-table" id="tablaPedidos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Entrega estimada</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody id="pedidosTbody">
                {{-- Filas generadas por JS --}}
            </tbody>
        </table>
        <div class="pedidos-empty" id="pedidosEmpty" style="display:none">
            <span>📭</span>
            <p>No hay pedidos registrados</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: CONFIRMAR PEDIDO
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalConfirmar" style="display:none">
    <div class="modal-card">
        <div class="modal-icon">✅</div>
        <h3 class="modal-title">Confirmar Pedido</h3>
        <p class="modal-desc">El pedido pasará a Entregas y se quitará de la lista activa.</p>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="window.closeModal('modalConfirmar')">Cancelar</button>
            <button class="btn btn-confirm" id="btnSiConfirmar" onclick="confirmarPedido()">✅ Confirmar pedido</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: RECHAZAR PEDIDO
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalRechazar" style="display:none">
    <div class="modal-card">
        <div class="modal-icon">🚫</div>
        <h3 class="modal-title">Rechazar Pedido</h3>
        <p class="modal-desc">El pedido quedará marcado como rechazado (podrás verlo al fondo de la lista con opacidad reducida).</p>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="window.closeModal('modalRechazar')">Cancelar</button>
            <button class="btn btn-danger" onclick="rechazarPedido()">🚫 Sí, rechazar</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: DESCRIPCIÓN DEL PEDIDO
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalDescripcion" style="display:none">
    <div class="modal-card modal-card--wide">
        <button class="modal-close" onclick="window.closeModal('modalDescripcion')">✕</button>
        <h3 class="modal-title">📄 Detalle del Pedido</h3>
        <div class="desc-meta">
            <span class="desc-meta-item">👤 <strong id="descCliente">—</strong></span>
            <span class="desc-meta-item">📅 <strong id="descFecha">—</strong></span>
            <span class="desc-meta-item">🕐 <strong id="descHora">—</strong></span>
            <span class="desc-meta-item" id="descEntregaWrap" style="display:none">📦 Entrega: <strong id="descEntrega">—</strong></span>
        </div>
        <div class="desc-productos-header">
            <span>Producto</span><span>Precio</span>
        </div>
        <ul class="desc-productos-list" id="descProductosList"></ul>
        <div class="desc-total">
            <span>Total</span>
            <span id="descTotal">$0.00</span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: AÑADIR / MODIFICAR PEDIDO
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalForm" style="display:none">
    <div class="modal-card modal-card--wide">
        <button class="modal-close" onclick="window.closeModal('modalForm')">✕</button>
        <h3 class="modal-title" id="modalFormTitle">＋ Nuevo Pedido</h3>
        <input type="hidden" id="formPedidoId">

        <div class="form-group">
            <label>Nombre del cliente</label>
            <input type="text" id="formCliente" class="form-input" placeholder="Ej: Juan Pérez">
        </div>

        <div class="form-group">
            <label>Hora estimada de entrega <span class="form-optional">(opcional)</span></label>
            <input type="time" id="formHoraEntrega" class="form-input form-input--time" placeholder="--:--">
            <small class="form-hint">📦 Indicá a qué hora necesita recibir el pedido para organizarlo por prioridad.</small>
        </div>

        <div class="form-group">
            <label>Productos <small>(nombre del producto)</small></label>
            <div class="form-productos-scroll" id="formProductosScroll">
                <div id="formProductosContainer"></div>
            </div>
            <button class="btn btn-add-producto" onclick="agregarFilaProducto()">＋ Agregar producto</button>
        </div>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="window.closeModal('modalForm')">Cancelar</button>
            <button class="btn btn-confirm" onclick="guardarPedido()">Guardar</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     ESTILOS DEL MÓDULO
══════════════════════════════════ --}}
<style>
/* ── Wrapper ── */
.pedidos-wrapper { display:flex; flex-direction:column; gap:1.2rem; }

/* ── Toolbar ── */
.pedidos-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08);
    border-radius:12px; padding:.9rem 1.2rem; gap:1rem; flex-wrap:wrap;
}
.pedidos-title { margin:0; font-size:1.2rem; font-weight:600; color:#e2e8f0; }
.pedidos-toolbar-actions { display:flex; gap:.6rem; flex-wrap:wrap; }

/* ── Botones toolbar ── */
.btn { display:inline-flex; align-items:center; gap:.4rem; border:none; border-radius:8px;
    padding:.5rem 1rem; font-size:.85rem; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-add    { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; }
.btn-add:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(34,197,94,.35); }
.btn-cancel  { background:rgba(255,255,255,.1); color:#e2e8f0; }
.btn-cancel:hover { background:rgba(255,255,255,.18); }
.btn-confirm { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; }
.btn-confirm:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(34,197,94,.35); }
.btn-danger  { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
.btn-danger:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(239,68,68,.35); }
.btn-add-producto { background:rgba(59,130,246,.15); color:#93c5fd; border:1px dashed #3b82f6;
    width:100%; margin-top:.5rem; justify-content:center; padding:.45rem; border-radius:8px; }
.btn-add-producto:hover { background:rgba(59,130,246,.25); }

/* ── Scroll contenedor de productos en formulario ── */
.form-productos-scroll { overflow: visible; }
.form-productos-scroll.has-scroll { overflow: visible; max-height: none; }
.form-productos-scroll::-webkit-scrollbar { width:5px; }
.form-productos-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,.04); border-radius:4px; }
.form-productos-scroll::-webkit-scrollbar-thumb { background:rgba(255,255,255,.18); border-radius:4px; }
.form-productos-scroll::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.3); }

/* ── Tabla ── */
.pedidos-table-container { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.07);
    border-radius:12px; overflow:hidden; }
.pedidos-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.pedidos-table thead tr { background:rgba(255,255,255,.06); }
.pedidos-table th { padding:.75rem 1rem; text-align:left; color:#94a3b8; font-weight:600;
    font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; }
.pedidos-table td { padding:.7rem 1rem; color:#e2e8f0; border-top:1px solid rgba(255,255,255,.05); vertical-align:middle; }
.pedidos-table tbody tr { transition:background .15s, opacity .2s; }
.pedidos-table tbody tr:hover:not(.row-rejected) { background:rgba(255,255,255,.04); }
.pedidos-table tbody tr.selected { background:rgba(59,130,246,.12); }
.col-acciones { text-align:right !important; }
.pedidos-empty { display:flex; flex-direction:column; align-items:center; gap:.5rem;
    padding:3rem; color:#64748b; font-size:.9rem; }
.pedidos-empty span { font-size:2.5rem; }

/* ── Separadores de sección ── */
.pedidos-section-sep td {
    background:rgba(255,255,255,.025);
    color:#64748b; font-size:.73rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.08em;
    padding:.45rem 1rem;
    border-top:2px solid rgba(255,255,255,.06);
}

/* ── Fila rechazada ── */
.row-rejected { opacity:.35; }
.row-rejected:hover { opacity:.55 !important; }

/* ── Badge estado ── */
.badge { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.75rem; font-weight:600; }
.badge-pending   { background:rgba(234,179,8,.15); color:#fbbf24; }
.badge-confirmed { background:rgba(34,197,94,.15);  color:#4ade80; }
.badge-rejected  { background:rgba(239,68,68,.15);  color:#f87171; }

/* ── Badge de proximidad de entrega ── */
.entrega-cell { display:flex; flex-direction:column; gap:.25rem; }
.entrega-time { font-size:.85rem; font-weight:600; color:#e2e8f0; }
.entrega-badge {
    display:inline-block; padding:.15rem .55rem; border-radius:20px;
    font-size:.7rem; font-weight:700; letter-spacing:.03em;
    white-space:nowrap; width:fit-content;
}
.entrega-badge--inminent  { background:rgba(239,68,68,.18);  color:#f87171; border:1px solid rgba(239,68,68,.35); animation:pulsar 1.4s ease-in-out infinite; }
.entrega-badge--soon      { background:rgba(251,146,60,.18); color:#fb923c; border:1px solid rgba(251,146,60,.35); }
.entrega-badge--ok        { background:rgba(34,197,94,.12);  color:#4ade80; border:1px solid rgba(34,197,94,.3); }
@keyframes pulsar { 0%,100%{opacity:1} 50%{opacity:.6} }
.entrega-none { color:#475569; font-size:.82rem; font-style:italic; }

/* ── Botones de fila ── */
.row-actions { display:flex; gap:.35rem; justify-content:flex-end; flex-wrap:wrap; }
.btn-row {
    display:inline-flex; align-items:center; gap:.3rem;
    border:none; border-radius:8px; padding:.38rem .7rem;
    font-size:.78rem; font-weight:600; cursor:pointer; transition:all .18s;
    white-space:nowrap;
}
.btn-row-confirm { background:rgba(34,197,94,.14);  color:#4ade80; }
.btn-row-confirm:hover { background:rgba(34,197,94,.28); transform:translateY(-1px); }
.btn-row-desc    { background:rgba(59,130,246,.13);  color:#93c5fd; }
.btn-row-desc:hover   { background:rgba(59,130,246,.28); transform:translateY(-1px); }
.btn-row-edit    { background:rgba(251,191,36,.13);  color:#fbbf24; }
.btn-row-edit:hover   { background:rgba(251,191,36,.28); transform:translateY(-1px); }
.btn-row-reject  { background:rgba(239,68,68,.12);   color:#f87171; }
.btn-row-reject:hover { background:rgba(239,68,68,.26); transform:translateY(-1px); }
.btn-row-restore { background:rgba(148,163,184,.12); color:#94a3b8; }
.btn-row-restore:hover { background:rgba(148,163,184,.25); transform:translateY(-1px); }

/* ── Modales ── */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(6px);
    display:none; align-items:center; justify-content:center; z-index:9999;
    animation:fadeIn .2s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.modal-card { background:#0f1523; border:1px solid rgba(255,255,255,.1); border-radius:16px;
    padding:2rem; max-width: min(490px, 90dvw); width:90%; position:relative;
    animation:slideUp .25s ease; }
.modal-card--wide { max-width: min(650px, 90dvw); }
@keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-close { position:absolute; top:.8rem; right:.8rem; background:rgba(255,255,255,.08);
    border:none; color:#94a3b8; border-radius:8px; width:28px; height:28px; cursor:pointer; font-size:.9rem; }
.modal-close:hover { background:rgba(255,255,255,.15); color:#e2e8f0; }
.modal-icon { font-size:2.8rem; text-align:center; margin-bottom:.5rem; }
.modal-title { text-align:center; font-size:1.1rem; color:#e2e8f0; margin:0 0 .5rem; }
.modal-desc  { text-align:center; color:#94a3b8; font-size:.875rem; margin:0 0 1.4rem; }
.modal-actions { display:flex; gap:.7rem; justify-content:center; margin-top:1.4rem; }

/* ── Modal descripción ── */
.desc-meta { display:flex; flex-wrap:wrap; gap:.8rem; margin:.8rem 0 1rem;
    background:rgba(255,255,255,.04); border-radius:10px; padding:.75rem 1rem; }
.desc-meta-item { color:#94a3b8; font-size:.85rem; }
.desc-meta-item strong { color:#e2e8f0; }
.desc-productos-header { display:flex; justify-content:space-between;
    color:#64748b; font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;
    padding:0 .5rem .4rem; border-bottom:1px solid rgba(255,255,255,.07); }
.desc-productos-list { list-style:none; padding:0; margin:.5rem 0; overflow-y:hidden; }
.desc-productos-list.has-scroll { overflow-y:auto; max-height:252px; }
.desc-productos-list::-webkit-scrollbar { width:5px; }
.desc-productos-list::-webkit-scrollbar-track { background:rgba(255,255,255,.04); border-radius:4px; }
.desc-productos-list::-webkit-scrollbar-thumb { background:rgba(255,255,255,.18); border-radius:4px; }
.desc-productos-list::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.3); }
.desc-productos-list li { display:flex; justify-content:space-between; padding:.55rem .5rem;
    border-bottom:1px solid rgba(255,255,255,.04); color:#e2e8f0; font-size:.875rem; }
.desc-total { display:flex; justify-content:space-between; padding:.8rem .5rem 0;
    border-top:2px solid rgba(255,255,255,.12); font-weight:700; color:#e2e8f0; font-size:1rem; margin-top:.5rem; }
.desc-total span:last-child { color:#4ade80; }

/* ── Formulario ── */
.form-group { display:flex; flex-direction:column; gap:.35rem; margin-bottom:1rem; }
.form-group label { color:#94a3b8; font-size:.82rem; font-weight:600; }
.form-optional { color:#475569; font-weight:400; font-size:.78rem; }
.form-hint { color:#6366f1; font-size:.76rem; margin-top:.1rem; }
.form-input { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1);
    border-radius:8px; padding:.55rem .8rem; color:#e2e8f0; font-size:.875rem; outline:none; width:100%; box-sizing:border-box; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,.2); }
.form-input--time { color-scheme:dark; max-width:160px; }
.producto-fila { display:flex; gap:.5rem; margin-bottom:.4rem; align-items:center; }
.producto-fila .form-input { flex:1; }
.btn-remove-producto { background:rgba(239,68,68,.15); color:#f87171; border:none;
    border-radius:6px; width:28px; height:28px; cursor:pointer; font-size:1rem; flex-shrink:0; }
.btn-remove-producto:hover { background:rgba(239,68,68,.3); }

/* ── Autocomplete ── */
.ac-wrapper { position:relative; flex:1; }
.ac-dropdown {
    position:absolute; bottom:calc(100% + 6px); left:0; right:0; z-index:10000;
    background:#131b2e; border:1px solid rgba(255,255,255,.12); border-radius:10px;
    max-height:200px; overflow-y:auto; display:none;
    box-shadow:0 -6px 24px rgba(0,0,0,.45);
    margin: 0; padding: 0; list-style: none;
}
.ac-dropdown.open { display:block; }
.ac-dropdown.has-scroll { overflow-y:auto; }
.ac-dropdown::-webkit-scrollbar { width:4px; }
.ac-dropdown::-webkit-scrollbar-track { background:rgba(255,255,255,.04); border-radius:4px; }
.ac-dropdown::-webkit-scrollbar-thumb { background:rgba(255,255,255,.22); border-radius:4px; }
.ac-dropdown::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.35); }
.ac-item {
    display:flex; justify-content:space-between; align-items:center;
    padding:.5rem .8rem; font-size:.82rem; color:#cbd5e1; cursor:pointer;
    border-bottom:1px solid rgba(255,255,255,.05); transition:background .12s;
}
.ac-item:last-child { border-bottom:none; }
.ac-item:hover, .ac-item.ac-active { background:rgba(59,130,246,.18); color:#e2e8f0; }
.ac-item .ac-precio { color:#4ade80; font-weight:700; font-size:.78rem; }
.ac-item .ac-match { color:#93c5fd; font-weight:700; }
</style>

{{-- ══════════════════════════════════
     JAVASCRIPT DEL MÓDULO
══════════════════════════════════ --}}
<script>
// ── Estado ──────────────────────────────────────────────
let pedidosLocal = [];
let pedidoAAccionar = null;

// ── Cargar Pedidos desde Servidor ────────────────────────
window.refreshPedidos = async function() {
    try {
        const res = await fetch('/panel/api/pedidos');
        const data = await res.json();
        pedidosLocal = data;
        renderTabla();
    } catch(e) {
        console.warn('Error al cargar pedidos del servidor:', e);
    }
};

// ── Utilidad: calcular urgencia de entrega ───────────────
function calcularUrgencia(horaEntrega) {
    if (!horaEntrega) return null;
    const now    = new Date();
    const [h, m] = horaEntrega.split(':').map(Number);
    const entrega = new Date(now.getFullYear(), now.getMonth(), now.getDate(), h, m);
    const diffMin = (entrega - now) / 60000;

    if (diffMin < 0)       return { label: 'Vencido',   cls: 'entrega-badge--inminent' };
    if (diffMin <= 15)     return { label: '⚡ ' + Math.round(diffMin) + ' min', cls: 'entrega-badge--inminent' };
    if (diffMin <= 45)     return { label: '🔶 ' + Math.round(diffMin) + ' min', cls: 'entrega-badge--soon' };
    return { label: '✅ ' + horaEntrega,  cls: 'entrega-badge--ok' };
}

// ── Ordenar pedidos: con hora→sin hora→rechazados ────────
function ordenarPedidos(lista) {
    const actCon    = lista.filter(p => p.estado !== 'rejected' && p.horaEntrega)
                           .sort((a,b) => a.horaEntrega.localeCompare(b.horaEntrega));
    const actSin    = lista.filter(p => p.estado !== 'rejected' && !p.horaEntrega);
    const rechaz    = lista.filter(p => p.estado === 'rejected');
    return { actCon, actSin, rechaz };
}

// ── Render tabla ────────────────────────────────────────
function renderTabla() {
    const tbody = document.getElementById('pedidosTbody');
    const empty = document.getElementById('pedidosEmpty');
    if (!tbody) return;
    tbody.innerHTML = '';

    // Filtrar para mostrar solo pendientes y rechazados en este módulo (confirmados van a Entregas)
    const filtered = pedidosLocal.filter(p => p.estado === 'pending' || p.estado === 'rejected');

    if (filtered.length === 0) { empty.style.display='flex'; return; }
    empty.style.display = 'none';

    const { actCon, actSin, rechaz } = ordenarPedidos(filtered);

    function crearSep(texto) {
        const tr = document.createElement('tr');
        tr.className = 'pedidos-section-sep';
        tr.innerHTML = `<td colspan="6">${texto}</td>`;
        return tr;
    }

    function crearFila(p) {
        // Celda entrega
        let entregaHTML = '<span class="entrega-none">Sin horario</span>';
        if (p.horaEntrega) {
            const urg = calcularUrgencia(p.horaEntrega);
            entregaHTML = `<div class="entrega-cell">
                <span class="entrega-time">🕐 ${p.horaEntrega}</span>
                <span class="entrega-badge ${urg.cls}">${urg.label}</span>
            </div>`;
        }

        // Badge estado
        let badgeEstado = '';
        if (p.estado === 'confirmed') badgeEstado = '<span class="badge badge-confirmed">Confirmado</span>';
        else if (p.estado === 'rejected') badgeEstado = '<span class="badge badge-rejected">Rechazado</span>';
        else badgeEstado = '<span class="badge badge-pending">Pendiente</span>';

        // Botones según estado
        let botonesHTML = '';
        if (p.estado === 'rejected') {
            botonesHTML = `
                <button class="btn-row btn-row-restore" onclick="restaurarPedido(${p.id})" title="Reactivar pedido">↩️ Reactivar</button>
                <button class="btn-row btn-row-desc"    onclick="abrirModalDescripcion(${p.id})" title="Ver detalle">📄 Detalle</button>
                <button class="btn-row btn-row-reject"  onclick="eliminarPedido(${p.id})" title="Eliminar pedido permanentemente">🗑️ Eliminar</button>`;
        } else {
            botonesHTML = `
                <button class="btn-row btn-row-confirm" onclick="abrirModalConfirmar(${p.id})" title="Confirmar pedido">✅ Confirmar</button>
                <button class="btn-row btn-row-edit"    onclick="abrirModalEditar(${p.id})"    title="Editar pedido">✏️ Editar</button>
                <button class="btn-row btn-row-desc"    onclick="abrirModalDescripcion(${p.id})" title="Ver detalle">📄 Detalle</button>
                <button class="btn-row btn-row-reject"  onclick="abrirModalRechazar(${p.id})"  title="Rechazar pedido">🚫 Rechazar</button>
                <button class="btn-row btn-row-reject"  onclick="eliminarPedido(${p.id})" title="Eliminar pedido permanentemente" style="background:rgba(239,68,68,0.08);">🗑️ Eliminar</button>`;
        }

        const tr = document.createElement('tr');
        tr.dataset.id = p.id;
        if (p.estado === 'rejected') tr.classList.add('row-rejected');
        tr.innerHTML = `
            <td style="color:#64748b;font-weight:600;font-size:.8rem">#${p.id}</td>
            <td><span style="font-weight:600">${p.cliente}</span></td>
            <td>${entregaHTML}</td>
            <td>${badgeEstado}</td>
            <td style="color:#4ade80;font-weight:700">$${p.total.toLocaleString('es-AR')}</td>
            <td><div class="row-actions">${botonesHTML}</div></td>`;
        return tr;
    }

    // Sección: con horario
    if (actCon.length > 0) {
        tbody.appendChild(crearSep('🕐 Con hora de entrega — ordenados por proximidad'));
        actCon.forEach(p => tbody.appendChild(crearFila(p)));
    }

    // Sección: sin horario
    if (actSin.length > 0) {
        tbody.appendChild(crearSep('📋 Sin hora de entrega definida'));
        actSin.forEach(p => tbody.appendChild(crearFila(p)));
    }

    // Sección: rechazados
    if (rechaz.length > 0) {
        tbody.appendChild(crearSep('🚫 Pedidos rechazados'));
        rechaz.forEach(p => tbody.appendChild(crearFila(p)));
    }
}

// ── Añadir ──────────────────────────────────────────────
function abrirModalAnadir() {
    document.getElementById('formPedidoId').value    = '';
    document.getElementById('formCliente').value     = '';
    document.getElementById('formHoraEntrega').value = '';
    document.getElementById('formProductosContainer').innerHTML = '';
    document.getElementById('modalFormTitle').textContent = '＋ Nuevo Pedido';
    agregarFilaProducto();
    window.openModal('modalForm');
    document.getElementById('formCliente').focus();
}

// ── Editar ──────────────────────────────────────────────
function abrirModalEditar(id) {
    const p = pedidosLocal.find(x => x.id === id);
    if (!p) return;
    document.getElementById('formPedidoId').value    = p.id;
    document.getElementById('formCliente').value     = p.cliente;
    document.getElementById('formHoraEntrega').value = p.horaEntrega || '';
    document.getElementById('formProductosContainer').innerHTML = '';
    document.getElementById('modalFormTitle').textContent = '✏️ Editar Pedido';
    
    p.productos.forEach(pr => {
        for(let i=0; i < pr.cantidad; i++) {
            agregarFilaProducto(pr.nombre, pr.precio);
        }
    });
    window.openModal('modalForm');
}

function actualizarScrollProductos() {
    const cont   = document.getElementById('formProductosContainer');
    const scroll = document.getElementById('formProductosScroll');
    if (!cont || !scroll) return;
    const filas  = cont.querySelectorAll('.producto-fila').length;
    scroll.classList.toggle('has-scroll', filas > 5);
    if (filas > 5) scroll.scrollTop = scroll.scrollHeight;
}

function agregarFilaProducto(nombre='', precio=0) {
    const cont = document.getElementById('formProductosContainer');
    if (!cont) return;
    const div  = document.createElement('div');
    div.className = 'producto-fila';
    div.dataset.precio = precio;
    div.innerHTML = `
        <div class="ac-wrapper">
            <input type="text" class="form-input prod-nombre" placeholder="Nombre del producto" value="${nombre}" autocomplete="off">
            <ul class="ac-dropdown"></ul>
        </div>
        <button class="btn-remove-producto" onclick="this.closest('.producto-fila').remove(); actualizarScrollProductos()">✕</button>`;
    cont.appendChild(div);
    initAutocomplete(div.querySelector('.prod-nombre'), div.querySelector('.ac-dropdown'), div);
    actualizarScrollProductos();
}

function initAutocomplete(input, dropdown, filaDiv) {
    let activeIdx = -1;

    function normalizar(s) {
        return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
    }

    async function mostrar(query) {
        const q = normalizar(query);
        if (!q) { cerrarDropdown(); return; }

        try {
            const res = await fetch(`/panel/api/products/search?q=${encodeURIComponent(query)}&tipo=reventa,elaborado`);
            const matches = await res.json();

            if (!matches.length) { cerrarDropdown(); return; }

            dropdown.innerHTML = '';
            activeIdx = -1;

            const exacto = matches.find(p => normalizar(p.name) === q);
            if (exacto) filaDiv.dataset.precio = exacto.price;

            matches.forEach((p, i) => {
                const li = document.createElement('li');
                li.className = 'ac-item';
                const idx = normalizar(p.name).indexOf(q);
                let hl = p.name;
                if (idx !== -1) {
                    hl = p.name.slice(0,idx)
                       + `<span class="ac-match">${p.name.slice(idx, idx+query.length)}</span>`
                       + p.name.slice(idx+query.length);
                }
                const tipoLabel = p.tipo === 'elaborado' ? '🍕' : '🛍️';
                li.innerHTML = `<span>${tipoLabel} ${hl}</span><span class="ac-precio">$${parseFloat(p.price).toLocaleString('es-AR')}</span>`;
                li.addEventListener('mousedown', e => {
                    e.preventDefault();
                    seleccionar(p);
                });
                li.dataset.idx = i;
                dropdown.appendChild(li);
            });
            dropdown.classList.toggle('has-scroll', matches.length > 3);
            dropdown.classList.add('open');
        } catch(e) {
            console.warn('Error en búsqueda de autocompletado:', e);
        }
    }

    function seleccionar(p) {
        input.value            = p.name;
        filaDiv.dataset.precio = p.price;
        cerrarDropdown();
    }

    function cerrarDropdown() {
        dropdown.classList.remove('open');
        dropdown.innerHTML = '';
        activeIdx = -1;
    }

    function setActivo(idx) {
        const items = dropdown.querySelectorAll('.ac-item');
        items.forEach(it => it.classList.remove('ac-active'));
        if (idx >= 0 && idx < items.length) {
            items[idx].classList.add('ac-active');
            items[idx].scrollIntoView({block:'nearest'});
        }
        activeIdx = idx;
    }

    input.addEventListener('input', () => mostrar(input.value));

    input.addEventListener('keydown', e => {
        const items = dropdown.querySelectorAll('.ac-item');
        if (!dropdown.classList.contains('open')) return;
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActivo(Math.min(activeIdx + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActivo(Math.max(activeIdx - 1, 0));
        } else if (e.key === 'Enter') {
            if (activeIdx >= 0 && items[activeIdx]) {
                e.preventDefault();
                items[activeIdx].dispatchEvent(new Event('mousedown'));
            } else {
                cerrarDropdown();
            }
        } else if (e.key === 'Escape') {
            cerrarDropdown();
        }
    });

    input.addEventListener('blur', () => setTimeout(cerrarDropdown, 150));
}

function guardarPedido() {
    const cliente      = document.getElementById('formCliente').value.trim();
    const horaEntrega  = document.getElementById('formHoraEntrega').value || null;
    if (!cliente) { alert('Ingresá el nombre del cliente.'); return; }

    const filas = document.querySelectorAll('#formProductosContainer .producto-fila');
    const itemsRaw = [];
    filas.forEach(f => {
        const nombre = f.querySelector('.prod-nombre').value.trim();
        if (nombre) itemsRaw.push(nombre);
    });
    if (itemsRaw.length === 0) { alert('Agregá al menos un producto.'); return; }

    // Agrupar filas del mismo producto e incrementar cantidad
    const agrupado = {};
    itemsRaw.forEach(n => {
        agrupado[n] = (agrupado[n] || 0) + 1;
    });

    const items = Object.keys(agrupado).map(n => ({
        product_name: n,
        quantity: agrupado[n]
    }));

    const id = document.getElementById('formPedidoId').value;
    const url = id ? `/panel/api/pedidos/${id}` : '/panel/api/pedidos';
    const method = id ? 'PUT' : 'POST';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            cliente: cliente,
            hora_entrega: horaEntrega,
            items: items
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.closeModal('modalForm');
            window.refreshPedidos();
            if (typeof window.refreshEntregas === 'function') {
                window.refreshEntregas();
            }
        } else {
            alert(data.message || 'Error al guardar el pedido.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al procesar el pedido.');
    });
}

// ── Confirmar pedido ─────────────────────────────────────
function abrirModalConfirmar(id) {
    pedidoAAccionar = id;
    window.openModal('modalConfirmar');
}
function confirmarPedido() {
    const id = pedidoAAccionar;
    if (!id) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/panel/api/pedidos/${id}/confirmar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.closeModal('modalConfirmar');
            window.refreshPedidos();
            if (typeof window.refreshEntregas === 'function') {
                window.refreshEntregas();
            }
        } else {
            alert(data.message || 'Error al confirmar el pedido.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al confirmar el pedido.');
    });
    pedidoAAccionar = null;
}

// ── Rechazar pedido (soft delete) ───────────────────────
function abrirModalRechazar(id) {
    pedidoAAccionar = id;
    window.openModal('modalRechazar');
}
function rechazarPedido() {
    const id = pedidoAAccionar;
    if (!id) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/panel/api/pedidos/${id}/rechazar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.closeModal('modalRechazar');
            window.refreshPedidos();
        } else {
            alert(data.message || 'Error al rechazar el pedido.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al rechazar el pedido.');
    });
    pedidoAAccionar = null;
}

// ── Restaurar pedido rechazado ───────────────────────────
function restaurarPedido(id) {
    if (!id) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/panel/api/pedidos/${id}/reactivar`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.refreshPedidos();
            if (typeof window.refreshEntregas === 'function') {
                window.refreshEntregas();
            }
        } else {
            alert(data.message || 'Error al reactivar el pedido.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al reactivar el pedido.');
    });
}

// ── Eliminar pedido ──────────────────────────────────────
function eliminarPedido(id) {
    if (!id) return;
    if (!confirm('¿Estás seguro de que deseas eliminar este pedido permanentemente?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/panel/api/pedidos/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.refreshPedidos();
            if (typeof window.refreshEntregas === 'function') {
                window.refreshEntregas();
            }
        } else {
            alert(data.message || 'Error al eliminar el pedido.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ocurrió un error al eliminar el pedido.');
    });
}

// ── Descripción ─────────────────────────────────────────
function abrirModalDescripcion(id) {
    const p = pedidosLocal.find(x => x.id === id);
    if (!p) return;
    document.getElementById('descCliente').textContent = p.cliente;
    document.getElementById('descFecha').textContent   = p.fecha;
    document.getElementById('descHora').textContent    = p.hora;

    const entregaWrap = document.getElementById('descEntregaWrap');
    if (p.horaEntrega) {
        document.getElementById('descEntrega').textContent = p.horaEntrega;
        entregaWrap.style.display = '';
    } else {
        entregaWrap.style.display = 'none';
    }

    const lista = document.getElementById('descProductosList');
    if (lista) {
        lista.innerHTML = '';
        lista.classList.toggle('has-scroll', p.productos.length > 7);
        p.productos.forEach(pr => {
            const li = document.createElement('li');
            const qtyLabel = pr.cantidad > 1 ? ` x${pr.cantidad}` : '';
            li.innerHTML = `<span>${pr.nombre}${qtyLabel}</span><span style="color:#4ade80">$${pr.subtotal.toLocaleString('es-AR')}</span>`;
            lista.appendChild(li);
        });
    }
    document.getElementById('descTotal').textContent = '$' + p.total.toLocaleString('es-AR');
    window.openModal('modalDescripcion');
}

// ── Refresco automático y Carga inicial ──────────────────
window.refreshPedidos();
setInterval(window.refreshPedidos, 60000);
</script>
