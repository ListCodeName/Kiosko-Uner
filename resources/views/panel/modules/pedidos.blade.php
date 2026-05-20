{{-- MÓDULO: PEDIDOS --}}
<div class="pedidos-wrapper">

    {{-- ── BARRA DE ACCIONES ── --}}
    <div class="pedidos-toolbar">
        <div class="pedidos-toolbar-left">
            <h2 class="pedidos-title">📋 Pedidos</h2>
        </div>
        <div class="pedidos-toolbar-actions">
            <button class="btn btn-add" id="btnAnadirPedido" onclick="abrirModalAnadir()">
                <span>＋</span> Añadir
            </button>
            <button class="btn btn-edit" id="btnModificarPedido" onclick="modificarPedidoSeleccionado()">
                <span>✏️</span> Modificar
            </button>
            <button class="btn btn-delete" id="btnEliminarPedido" onclick="eliminarPedidoSeleccionado()">
                <span>🗑️</span> Eliminar
            </button>
        </div>
    </div>

    {{-- ── TABLA DE PEDIDOS ── --}}
    <div class="pedidos-table-container">
        <table class="pedidos-table" id="tablaPedidos">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"></th>
                    <th>#</th>
                    <th>Cliente</th>
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
     MODAL: CONFIRMAR PEDIDO (👍)
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalConfirmar" style="display:none">
    <div class="modal-card">
        <div class="modal-icon modal-icon--warn">👍</div>
        <h3 class="modal-title">¿Está seguro?</h3>
        <p class="modal-desc">Esta acción confirmará el pedido y lo eliminará de la lista.</p>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="cerrarModal('modalConfirmar')">Cancelar</button>
            <button class="btn btn-confirm" id="btnSiConfirmar" onclick="confirmarPedido()">Sí, confirmar</button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     MODAL: DESCRIPCIÓN DEL PEDIDO
══════════════════════════════════ --}}
<div class="modal-overlay" id="modalDescripcion" style="display:none">
    <div class="modal-card modal-card--wide">
        <button class="modal-close" onclick="cerrarModal('modalDescripcion')">✕</button>
        <h3 class="modal-title">📄 Descripción del Pedido</h3>
        <div class="desc-meta">
            <span class="desc-meta-item">👤 <strong id="descCliente">—</strong></span>
            <span class="desc-meta-item">📅 <strong id="descFecha">—</strong></span>
            <span class="desc-meta-item">🕐 <strong id="descHora">—</strong></span>
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
        <button class="modal-close" onclick="cerrarModal('modalForm')">✕</button>
        <h3 class="modal-title" id="modalFormTitle">＋ Nuevo Pedido</h3>
        <input type="hidden" id="formPedidoId">
        <div class="form-group">
            <label>Nombre del cliente</label>
            <input type="text" id="formCliente" class="form-input" placeholder="Ej: Juan Pérez">
        </div>
        <div class="form-group">
            <label>Productos <small>(nombre y precio)</small></label>
            <div class="form-productos-scroll" id="formProductosScroll">
                <div id="formProductosContainer"></div>
            </div>
            <button class="btn btn-add-producto" onclick="agregarFilaProducto()">＋ Agregar producto</button>
        </div>
        <div class="modal-actions">
            <button class="btn btn-cancel" onclick="cerrarModal('modalForm')">Cancelar</button>
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
.btn-edit   { background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; }
.btn-edit:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(59,130,246,.35); }
.btn-delete { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
.btn-delete:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(239,68,68,.35); }
.btn-cancel  { background:rgba(255,255,255,.1); color:#e2e8f0; }
.btn-cancel:hover { background:rgba(255,255,255,.18); }
.btn-confirm { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; }
.btn-confirm:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(34,197,94,.35); }
.btn-add-producto { background:rgba(59,130,246,.15); color:#93c5fd; border:1px dashed #3b82f6;
    width:100%; margin-top:.5rem; justify-content:center; padding:.45rem; border-radius:8px; }
.btn-add-producto:hover { background:rgba(59,130,246,.25); }

/* ── Scroll contenedor de productos en formulario ── */
.form-productos-scroll { overflow-y:hidden; transition:max-height .25s ease; }
.form-productos-scroll.has-scroll { overflow-y:auto; max-height:220px; }
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
.pedidos-table td { padding:.75rem 1rem; color:#e2e8f0; border-top:1px solid rgba(255,255,255,.05); }
.pedidos-table tbody tr { transition:background .15s; }
.pedidos-table tbody tr:hover { background:rgba(255,255,255,.04); }
.pedidos-table tbody tr.selected { background:rgba(59,130,246,.12); }
.col-acciones { text-align:right !important; }
.pedidos-empty { display:flex; flex-direction:column; align-items:center; gap:.5rem;
    padding:3rem; color:#64748b; font-size:.9rem; }
.pedidos-empty span { font-size:2.5rem; }

/* ── Badge estado ── */
.badge { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.75rem; font-weight:600; }
.badge-pending  { background:rgba(234,179,8,.15); color:#fbbf24; }
.badge-confirmed{ background:rgba(34,197,94,.15); color:#4ade80; }

/* ── Botones de fila ── */
.btn-row { border:none; border-radius:8px; padding:.4rem .65rem; font-size:1rem; cursor:pointer; transition:all .18s; }
.btn-thumb { background:rgba(34,197,94,.12); color:#4ade80; }
.btn-thumb:hover { background:rgba(34,197,94,.25); transform:scale(1.08); }
.btn-desc  { background:rgba(59,130,246,.12); color:#93c5fd; font-size:.75rem; font-weight:600; }
.btn-desc:hover  { background:rgba(59,130,246,.25); transform:scale(1.05); }
.row-actions { display:flex; gap:.4rem; justify-content:flex-end; }

/* ── Modales ── */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(6px);
    display:flex; align-items:center; justify-content:center; z-index:9999;
    animation:fadeIn .2s ease; }
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.modal-card { background:#0f1523; border:1px solid rgba(255,255,255,.1); border-radius:16px;
    padding:2rem; max-width:420px; width:90%; position:relative;
    animation:slideUp .25s ease; }
.modal-card--wide { max-width:540px; }
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
.form-input { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1);
    border-radius:8px; padding:.55rem .8rem; color:#e2e8f0; font-size:.875rem; outline:none; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,.2); }
.producto-fila { display:flex; gap:.5rem; margin-bottom:.4rem; align-items:center; }
.producto-fila .form-input { flex:1; }
.producto-fila .form-input.precio { width:90px; flex:none; }
.btn-remove-producto { background:rgba(239,68,68,.15); color:#f87171; border:none;
    border-radius:6px; width:28px; height:28px; cursor:pointer; font-size:1rem; flex-shrink:0; }
.btn-remove-producto:hover { background:rgba(239,68,68,.3); }

/* ── Autocomplete ── */
.ac-wrapper { position:relative; flex:1; }
.ac-dropdown {
    position:absolute; bottom:calc(100% + 6px); left:0; right:0; z-index:10000;
    background:#131b2e; border:1px solid rgba(255,255,255,.12); border-radius:10px;
    max-height:111px; overflow-y:hidden; display:none;
    box-shadow:0 -6px 24px rgba(0,0,0,.45);
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
// ── Catálogo hardcodeado (para pruebas) ─────────────────
const CATALOGO = [
    {nombre:'Sándwich de miga',          precio:1200},
    {nombre:'Sándwich de pollo',          precio:1400},
    {nombre:'Sándwich de jamón y queso',  precio:1100},
    {nombre:'Empanada de carne',          precio:700},
    {nombre:'Empanada de jamón y queso',  precio:700},
    {nombre:'Empanada de pollo',          precio:750},
    {nombre:'Empanada de verdura',        precio:650},
    {nombre:'Medialunas x4',              precio:1500},
    {nombre:'Facturas x6',                precio:1800},
    {nombre:'Alfajor de chocolate',       precio:450},
    {nombre:'Alfajor de maicena',         precio:380},
    {nombre:'Café solo',                  precio:700},
    {nombre:'Café con leche',             precio:950},
    {nombre:'Capuchino',                  precio:1100},
    {nombre:'Té con limón',               precio:650},
    {nombre:'Jugo de naranja',            precio:800},
    {nombre:'Agua mineral 500ml',         precio:500},
    {nombre:'Gaseosa 500ml',              precio:600},
    {nombre:'Yogur con cereales',         precio:850},
    {nombre:'Ensalada de frutas',         precio:900},
];

// ── Estado ──────────────────────────────────────────────
let pedidos = [
    {
        id: 1, cliente: 'María González', estado: 'pending',
        fecha: '2026-05-08', hora: '09:15',
        productos: [
            {nombre:'Sándwich de miga', precio:1200},
            {nombre:'Jugo de naranja', precio:800},
            {nombre:'Alfajor', precio:450},
        ]
    },
    {
        id: 2, cliente: 'Carlos Ramírez', estado: 'pending',
        fecha: '2026-05-08', hora: '10:32',
        productos: [
            {nombre:'Medialunas x4', precio:1500},
            {nombre:'Café con leche', precio:950},
        ]
    },
    {
        id: 3, cliente: 'Lucía Fernández', estado: 'pending',
        fecha: '2026-05-08', hora: '11:05',
        productos: [
            {nombre:'Empanada de carne', precio:700},
            {nombre:'Empanada de jamón', precio:700},
            {nombre:'Agua mineral', precio:500},
        ]
    },
];
let nextId = 4;
let pedidoAConfirmar = null;

// ── Render tabla ────────────────────────────────────────
function renderTabla() {
    const tbody = document.getElementById('pedidosTbody');
    const empty = document.getElementById('pedidosEmpty');
    tbody.innerHTML = '';
    if (pedidos.length === 0) { empty.style.display='flex'; return; }
    empty.style.display = 'none';
    pedidos.forEach(p => {
        const total = p.productos.reduce((s,x)=>s+x.precio,0);
        const badge = p.estado==='confirmed'
            ? '<span class="badge badge-confirmed">Confirmado</span>'
            : '<span class="badge badge-pending">Pendiente</span>';
        const tr = document.createElement('tr');
        tr.dataset.id = p.id;
        tr.innerHTML = `
            <td><input type="checkbox" class="check-pedido" value="${p.id}"></td>
            <td>#${p.id}</td>
            <td>${p.cliente}</td>
            <td>${badge}</td>
            <td style="color:#4ade80;font-weight:600">$${total.toLocaleString('es-AR')}</td>
            <td>
                <div class="row-actions">
                    <button class="btn-row btn-thumb" title="Confirmar pedido" onclick="abrirModalConfirmar(${p.id})">👍</button>
                    <button class="btn-row btn-desc"  title="Ver descripción"  onclick="abrirModalDescripcion(${p.id})">Descripción</button>
                </div>
            </td>`;
        tbody.appendChild(tr);
    });
    document.getElementById('checkAll').checked = false;
}

// ── Check todos ─────────────────────────────────────────
function toggleCheckAll(cb) {
    document.querySelectorAll('.check-pedido').forEach(c => c.checked = cb.checked);
}

// ── Añadir ──────────────────────────────────────────────
function abrirModalAnadir() {
    document.getElementById('formPedidoId').value = '';
    document.getElementById('formCliente').value = '';
    document.getElementById('formProductosContainer').innerHTML = '';
    document.getElementById('modalFormTitle').textContent = '＋ Nuevo Pedido';
    agregarFilaProducto();
    document.getElementById('modalForm').style.display = 'flex';
}

function actualizarScrollProductos() {
    const cont   = document.getElementById('formProductosContainer');
    const scroll = document.getElementById('formProductosScroll');
    const filas  = cont.querySelectorAll('.producto-fila').length;
    scroll.classList.toggle('has-scroll', filas > 5);
    if (filas > 5) scroll.scrollTop = scroll.scrollHeight;
}

function agregarFilaProducto(nombre='', precio='') {
    const cont = document.getElementById('formProductosContainer');
    const div  = document.createElement('div');
    div.className = 'producto-fila';
    div.innerHTML = `
        <div class="ac-wrapper">
            <input type="text" class="form-input prod-nombre" placeholder="Nombre del producto" value="${nombre}" autocomplete="off">
            <ul class="ac-dropdown"></ul>
        </div>
        <input type="number" class="form-input precio prod-precio" placeholder="$0" min="0" value="${precio}">
        <button class="btn-remove-producto" onclick="this.closest('.producto-fila').remove(); actualizarScrollProductos()">✕</button>`;
    cont.appendChild(div);
    initAutocomplete(div.querySelector('.prod-nombre'), div.querySelector('.ac-dropdown'), div.querySelector('.prod-precio'));
    actualizarScrollProductos();
}

function initAutocomplete(input, dropdown, precioInput) {
    let activeIdx = -1;

    function normalizar(s) {
        return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');
    }

    function mostrar(query) {
        const q = normalizar(query);
        if (!q) { cerrarDropdown(); return; }
        const matches = CATALOGO.filter(p => normalizar(p.nombre).includes(q));
        if (!matches.length) { cerrarDropdown(); return; }

        // Precio automático si coincidencia exacta
        const exacto = CATALOGO.find(p => normalizar(p.nombre) === q);
        if (exacto) precioInput.value = exacto.precio;

        dropdown.innerHTML = '';
        activeIdx = -1;
        matches.forEach((p, i) => {
            const li = document.createElement('li');
            li.className = 'ac-item';
            // Resaltar parte coincidente
            const idx = normalizar(p.nombre).indexOf(q);
            const hl  = p.nombre.slice(0,idx)
                      + `<span class="ac-match">${p.nombre.slice(idx, idx+query.length)}</span>`
                      + p.nombre.slice(idx+query.length);
            li.innerHTML = `<span>${hl}</span><span class="ac-precio">$${p.precio.toLocaleString('es-AR')}</span>`;
            li.addEventListener('mousedown', e => {
                e.preventDefault();
                seleccionar(p);
            });
            li.dataset.idx = i;
            dropdown.appendChild(li);
        });
        // Scroll solo si hay más de 3 resultados
        dropdown.classList.toggle('has-scroll', matches.length > 3);
        dropdown.classList.add('open');
    }

    function seleccionar(p) {
        input.value      = p.nombre;
        precioInput.value = p.precio;
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
                const idx  = parseInt(items[activeIdx].dataset.idx);
                const q    = normalizar(input.value);
                const matches = CATALOGO.filter(p => normalizar(p.nombre).includes(q));
                seleccionar(matches[idx]);
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
    const cliente = document.getElementById('formCliente').value.trim();
    if (!cliente) { alert('Ingresá el nombre del cliente.'); return; }
    const filas = document.querySelectorAll('#formProductosContainer .producto-fila');
    const productos = [];
    filas.forEach(f => {
        const nombre = f.querySelector('.prod-nombre').value.trim();
        const precio = parseFloat(f.querySelector('.prod-precio').value) || 0;
        if (nombre) productos.push({nombre, precio});
    });
    if (productos.length === 0) { alert('Agregá al menos un producto.'); return; }
    const id = document.getElementById('formPedidoId').value;
    const now = new Date();
    const fecha = now.toISOString().slice(0,10);
    const hora  = now.toTimeString().slice(0,5);
    if (id) {
        const p = pedidos.find(x=>x.id==id);
        if (p) { p.cliente=cliente; p.productos=productos; }
    } else {
        pedidos.push({id:nextId++, cliente, estado:'pending', fecha, hora, productos});
    }
    cerrarModal('modalForm');
    renderTabla();
}

// ── Modificar ───────────────────────────────────────────
function modificarPedidoSeleccionado() {
    const checked = [...document.querySelectorAll('.check-pedido:checked')];
    if (checked.length !== 1) { alert('Seleccioná exactamente un pedido para modificar.'); return; }
    const id = parseInt(checked[0].value);
    const p  = pedidos.find(x=>x.id===id);
    document.getElementById('formPedidoId').value = p.id;
    document.getElementById('formCliente').value  = p.cliente;
    document.getElementById('formProductosContainer').innerHTML = '';
    document.getElementById('modalFormTitle').textContent = '✏️ Modificar Pedido';
    p.productos.forEach(pr => agregarFilaProducto(pr.nombre, pr.precio));
    document.getElementById('modalForm').style.display = 'flex';
}

// ── Eliminar (por checkbox) ──────────────────────────────
function eliminarPedidoSeleccionado() {
    const checked = [...document.querySelectorAll('.check-pedido:checked')];
    if (checked.length === 0) { alert('Seleccioná al menos un pedido para eliminar.'); return; }
    if (!confirm(`¿Eliminar ${checked.length} pedido(s) seleccionado(s)?`)) return;
    const ids = checked.map(c=>parseInt(c.value));
    pedidos = pedidos.filter(p=>!ids.includes(p.id));
    renderTabla();
}

// ── Confirmar pedido (👍) ────────────────────────────────
function abrirModalConfirmar(id) {
    pedidoAConfirmar = id;
    document.getElementById('modalConfirmar').style.display = 'flex';
}
function confirmarPedido() {
    const p = pedidos.find(x=>x.id === pedidoAConfirmar);
    if (p) {
        // Pasar a entregas
        if (typeof entregas !== 'undefined') {
            entregas.push({ ...p, confirmadoEn: new Date().toLocaleString('es-AR') });
            if (typeof renderEntregas === 'function') renderEntregas();
        }
        pedidos = pedidos.filter(x=>x.id !== pedidoAConfirmar);
    }
    pedidoAConfirmar = null;
    cerrarModal('modalConfirmar');
    renderTabla();
}

// ── Descripción ─────────────────────────────────────────
function abrirModalDescripcion(id) {
    const p = pedidos.find(x=>x.id===id);
    document.getElementById('descCliente').textContent = p.cliente;
    document.getElementById('descFecha').textContent   = p.fecha;
    document.getElementById('descHora').textContent    = p.hora;
    const lista = document.getElementById('descProductosList');
    lista.innerHTML = '';
    // Scroll automático si hay más de 7 productos
    lista.classList.toggle('has-scroll', p.productos.length > 7);
    let total = 0;
    p.productos.forEach(pr => {
        total += pr.precio;
        const li = document.createElement('li');
        li.innerHTML = `<span>${pr.nombre}</span><span style="color:#4ade80">$${pr.precio.toLocaleString('es-AR')}</span>`;
        lista.appendChild(li);
    });
    document.getElementById('descTotal').textContent = '$' + total.toLocaleString('es-AR');
    document.getElementById('modalDescripcion').style.display = 'flex';
}


// ── Cerrar modal ─────────────────────────────────────────
function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if(e.target===m) cerrarModal(m.id); });
});

// ── Iniciar ──────────────────────────────────────────────
renderTabla();
</script>
