{{-- MÓDULO: COMPRAS --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <div class="compras-summary" id="comprasSummary">
        <span class="summary-badge" id="comprasTotalCount">0 compras</span>
        <span class="summary-badge highlight" id="comprasTotalMonto">$0,00 total</span>
    </div>
    <button class="btn btn-gen" id="btnNewCompra">＋ Nueva Compra</button>
</div>

{{-- Grid de tarjetas --}}
<div class="compras-grid" id="comprasGrid"></div>

<div class="table-empty" id="comprasEmpty" style="display:none">
    <div class="empty-state" style="min-height:300px">
        <div class="empty-state-illustration">
            <div class="empty-state-pulse"></div>
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 10h4l6 24h24l4-16H18" stroke="#2d8cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.8"/>
                <circle cx="26" cy="38" r="3" fill="#6ab4ff" opacity="0.9"/>
                <circle cx="38" cy="38" r="3" fill="#6ab4ff" opacity="0.9"/>
                <path d="M20 22h20" stroke="#2d8cff" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
            </svg>
        </div>
        <h2 class="empty-state-title">Sin compras registradas</h2>
        <p class="empty-state-desc">Todavía no se registraron compras de mercadería.<br>Comenzá registrando tu primer ingreso de stock.</p>
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="comprasToast"></div>

{{-- ══════════════════ MODAL: NUEVA COMPRA ══════════════════ --}}
<div class="modal-overlay" id="compraCreateModal" style="display: none !important;">
    <div class="modal" style="max-width:640px">
        <div class="modal-header">
            <h3 class="modal-title">🛒 Nueva Compra</h3>
            <button class="modal-close" id="compraModalClose">✕</button>
        </div>
        <form class="modal-body" id="compraCreateForm">
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Fecha de compra <span style="color:#ef4444">*</span></label>
                    <input class="form-input" type="date" name="fecha" id="compra-fecha" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Observaciones</label>
                    <input class="form-input" name="observaciones" placeholder="Ej: Compra semanal...">
                </div>
            </div>

            {{-- Líneas de productos --}}
            <div style="margin-top:4px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <label class="form-label" style="margin:0">Productos <span style="color:#ef4444">*</span></label>
                    <button type="button" class="btn btn-gen" id="btnAddItem" style="padding:5px 12px;font-size:0.8rem">＋ Añadir producto</button>
                </div>

                {{-- Cabecera items --}}
                <div class="items-header">
                    <span style="flex:2">Producto</span>
                    <span style="width:80px;text-align:center">Cant.</span>
                    <span style="width:100px;text-align:right">Precio unit.</span>
                    <span style="width:100px;text-align:right">Subtotal</span>
                    <span style="width:32px"></span>
                </div>

                <div id="compraItemsContainer"></div>

                {{-- Total --}}
                <div class="compra-total-row">
                    <span>TOTAL</span>
                    <span id="compraModalTotal">$0,00</span>
                </div>
            </div>

            <div class="modal-footer" style="margin-top:8px">
                <button type="button" class="btn-cancel" id="compraCancelBtn">Cancelar</button>
                <button type="submit" class="btn-submit">Registrar Compra</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: ELIMINAR ══════════════════ --}}
<div class="modal-overlay" id="compraDeleteModal" style="display: none !important;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Eliminación</h3>
            <button class="modal-close" id="compraDelClose">✕</button>
        </div>
        <form class="modal-body" id="compraDeleteForm">
            <input type="hidden" id="compra-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">¿Estás seguro de que querés eliminar la compra de la fecha:</p>
            <p style="color:#ef4444;font-weight:700;font-size:1rem;margin-bottom:8px" id="compra-del-fecha"></p>
            <p style="color:var(--text-primary);font-size:0.9rem;margin-bottom:16px">
                <strong>Productos incluidos:</strong><br>
                <span id="compra-del-productos" style="color:var(--text-secondary);"></span>
            </p>
            <p style="color:var(--text-muted);font-size:.8rem">Esta acción eliminará también todos sus productos. No se puede deshacer.</p>
            <div class="modal-footer" style="margin-top:16px">
                <button type="button" class="btn-cancel" id="compraDelCancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    let comprasData = [];
    let loaded = false;
    let itemCount = 0;

    /* ── API ── */
    async function api(url, method = 'GET', body = null) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res = await fetch(url, opts);
        const data = await res.json();
        if (!res.ok) throw data;
        return data;
    }

    /* ── Toast ── */
    function toast(msg, type = 'success') {
        const t = document.getElementById('comprasToast');
        if (!t) return;
        t.textContent = msg;
        t.className = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ── Formatear peso ── */
    function formatMoney(v) {
        return '$' + Number(v).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* ── Modales ── */
    function openModal(id)  { document.getElementById(id)?.classList.add('visible'); }
    function closeModal(id) { document.getElementById(id)?.classList.remove('visible'); }

    ['compraModalClose','compraCancelBtn'].forEach(id =>
        document.getElementById(id)?.addEventListener('click', () => closeModal('compraCreateModal')));
    ['compraDelClose','compraDelCancel'].forEach(id =>
        document.getElementById(id)?.addEventListener('click', () => closeModal('compraDeleteModal')));

    ['compraCreateModal','compraDeleteModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', e => {
            if (e.target === e.currentTarget) closeModal(id);
        });
    });

    /* ── Render tarjetas ── */
    function renderCards(data) {
        const grid  = document.getElementById('comprasGrid');
        const empty = document.getElementById('comprasEmpty');
        const countBadge = document.getElementById('comprasTotalCount');
        const montoBadge = document.getElementById('comprasTotalMonto');
        if (!grid) return;

        const totalMonto = data.reduce((s, c) => s + c.total, 0);
        if (countBadge) countBadge.textContent = `${data.length} compra${data.length !== 1 ? 's' : ''}`;
        if (montoBadge) montoBadge.textContent = formatMoney(totalMonto) + ' total';

        if (!data.length) {
            grid.innerHTML = '';
            if (empty) empty.style.display = 'flex';
            return;
        }
        if (empty) empty.style.display = 'none';

        grid.innerHTML = data.map(c => `
            <div class="compra-card" data-id="${c.id}">
                <div class="compra-card-header">
                    <div class="compra-card-date">
                        <span class="compra-date-icon">📅</span>
                        <span>${c.fecha}</span>
                    </div>
                    <button class="action-btn danger compra-del-btn" title="Eliminar compra" onclick="COMPRA.del(${c.id})">🗑️</button>
                </div>

                ${c.observaciones ? `<p class="compra-obs">${c.observaciones}</p>` : ''}

                <div class="compra-items-list">
                    <div class="compra-items-head">
                        <span style="flex:1">Producto</span>
                        <span style="width:60px;text-align:center">Cant.</span>
                        <span style="width:90px;text-align:right">P. Unit.</span>
                        <span style="width:90px;text-align:right">Subtotal</span>
                    </div>
                    ${c.items.map(i => `
                        <div class="compra-item-row">
                            <span class="compra-item-name" style="flex:1">${i.producto_nombre}</span>
                            <span style="width:60px;text-align:center;color:var(--text-secondary)">${i.cantidad % 1 === 0 ? i.cantidad : i.cantidad.toFixed(2)}</span>
                            <span style="width:90px;text-align:right;color:var(--text-secondary)">${formatMoney(i.precio_unitario)}</span>
                            <span style="width:90px;text-align:right;color:var(--blue-light);font-weight:600">${formatMoney(i.subtotal)}</span>
                        </div>
                    `).join('')}
                </div>

                <div class="compra-card-footer">
                    <span style="color:var(--text-muted);font-size:0.8rem">${c.items.length} producto${c.items.length !== 1 ? 's' : ''}</span>
                    <div class="compra-total">
                        <span style="color:var(--text-secondary);font-size:0.8rem">Total</span>
                        <span class="compra-total-amount">${formatMoney(c.total)}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    /* ── Load ── */
    async function load() {
        if (loaded) return;
        try {
            const data = await api('/api/compras');
            comprasData = data.compras || [];
            loaded = true;
            renderCards(comprasData);
        } catch (e) { toast('Error al cargar compras', 'error'); }
    }

    /* Observer de activación */
    const section = document.querySelector('[data-module-content="compras"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) load();
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
    }

    /* ── Items del formulario ── */
    function calcModalTotal() {
        let total = 0;
        document.querySelectorAll('.compra-item-row-form').forEach(row => {
            const cant  = parseFloat(row.querySelector('.item-qty').value)  || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const sub   = cant * price;
            total += sub;
            const subEl = row.querySelector('.item-subtotal');
            if (subEl) subEl.textContent = formatMoney(sub);
        });
        const totalEl = document.getElementById('compraModalTotal');
        if (totalEl) totalEl.textContent = formatMoney(total);
    }

    function addItemRow(name = '', qty = '', price = '') {
        itemCount++;
        const cont = document.getElementById('compraItemsContainer');
        if (!cont) return;
        const div = document.createElement('div');
        div.className = 'compra-item-row-form';
        div.dataset.idx = itemCount;
        div.innerHTML = `
            <input class="form-input item-name" placeholder="Nombre del producto" value="${name}" required style="flex:2;min-width:0">
            <input class="form-input item-qty"   type="number" step="0.01" min="0.01" placeholder="Cant." value="${qty}" required style="width:72px;text-align:center">
            <input class="form-input item-price" type="number" step="0.01" min="0"    placeholder="$0,00" value="${price}" required style="width:96px;text-align:right">
            <span class="item-subtotal" style="width:96px;text-align:right;color:var(--blue-light);font-weight:600;font-size:0.88rem;align-self:center">$0,00</span>
            <button type="button" class="item-remove-btn" title="Quitar">✕</button>
        `;
        div.querySelector('.item-remove-btn').addEventListener('click', () => {
            div.remove();
            calcModalTotal();
        });
        div.querySelector('.item-qty').addEventListener('input', calcModalTotal);
        div.querySelector('.item-price').addEventListener('input', calcModalTotal);
        cont.appendChild(div);
        if (qty && price) calcModalTotal();
    }

    document.getElementById('btnAddItem')?.addEventListener('click', () => addItemRow());

    /* Abrir modal nueva compra */
    document.getElementById('btnNewCompra')?.addEventListener('click', () => {
        document.getElementById('compraCreateForm')?.reset();
        document.getElementById('compraItemsContainer').innerHTML = '';
        itemCount = 0;
        // Fecha por defecto: hoy
        const today = new Date().toISOString().slice(0, 10);
        const fechaInput = document.getElementById('compra-fecha');
        if (fechaInput) fechaInput.value = today;
        document.getElementById('compraModalTotal').textContent = '$0,00';
        addItemRow(); // Fila inicial
        // openModal('compraCreateModal'); // TEMPORALMENTE SUPRIMIDO POR SOLICITUD
    });

    /* Submit nueva compra */
    document.getElementById('compraCreateForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const items = [];
        document.querySelectorAll('.compra-item-row-form').forEach(row => {
            items.push({
                producto_nombre: row.querySelector('.item-name').value.trim(),
                cantidad:        parseFloat(row.querySelector('.item-qty').value),
                precio_unitario: parseFloat(row.querySelector('.item-price').value),
            });
        });
        if (!items.length) { toast('Agregá al menos un producto', 'error'); return; }

        const body = { fecha: fd.get('fecha'), observaciones: fd.get('observaciones') || null, items };
        try {
            const data = await api('/api/compras', 'POST', body);
            toast(data.message);
            closeModal('compraCreateModal');
            comprasData.unshift(data.compra);
            renderCards(comprasData);
        } catch (err) {
            toast(err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al registrar', 'error');
        }
    });

    /* Delete */
    window.COMPRA = window.COMPRA || {};
    COMPRA.del = function (id) {
        const c = comprasData.find(x => x.id === id);
        if (!c) return;
        document.getElementById('compra-del-id').value = id;
        document.getElementById('compra-del-fecha').textContent = c.fecha;
        
        const prodNames = c.items.map(i => i.producto_nombre).join(', ');
        const elProductos = document.getElementById('compra-del-productos');
        if (elProductos) elProductos.textContent = prodNames || 'Ninguno';

        // openModal('compraDeleteModal'); // TEMPORALMENTE SUPRIMIDO POR SOLICITUD
    };

    document.getElementById('compraDeleteForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const id = document.getElementById('compra-del-id').value;
        try {
            const data = await api(`/api/compras/${id}`, 'DELETE');
            toast(data.message);
            closeModal('compraDeleteModal');
            comprasData = comprasData.filter(x => x.id != id);
            renderCards(comprasData);
        } catch (err) { toast(err.message || 'Error al eliminar', 'error'); }
    });
})();
</script>
