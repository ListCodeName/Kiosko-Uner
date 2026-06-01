{{-- MÓDULO: COMPRAS --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <div class="compras-summary" id="comprasSummary">
        <span class="summary-badge" id="comprasTotalCount">0 compras</span>
        <span class="summary-badge highlight" id="comprasTotalMonto">$0,00 total</span>
    </div>
    <div style="display:flex;gap:0.5rem">
        <button class="btn btn-secondary" id="btnSyncCompra" style="background:rgba(108,99,255,.1);color:#8b85ff;border:1px solid rgba(108,99,255,.2);padding:10px 18px;font-size:0.88rem;border-radius:10px;cursor:pointer;font-weight:600;transition:all .2s">🔄 Sincronizar</button>
        <button class="btn btn-gen" id="btnNewCompra">＋ Nueva Compra</button>
    </div>
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
            </svg>
        </div>
        <h2 class="empty-state-title">Sin compras registradas</h2>
        <p class="empty-state-desc">Registrá tu primera compra de mercadería para comenzar a controlar el stock.</p>
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="comprasToast"></div>

<style>
/* ── AUTOCOMPLETADO ── */
.ac-wrap { position:relative; }
.ac-input-row { display:flex;gap:.4rem;align-items:center; }
.ac-input-row .form-input { flex:1; }
.ac-dropdown { height:125px; position:absolute;top:100%;left:0;right:0;background:#1a1d2e;border:1px solid rgba(108,99,255,.35);border-radius:8px;z-index:600;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.5);margin-top:2px;max-height:200px;overflow-y:auto; }
.ac-item { display:flex;align-items:center;gap:.5rem;padding:.55rem .9rem;cursor:pointer;font-size:.88rem;color:#e8eaf0;transition:background .12s; }
.ac-item:hover,.ac-item.selected { background:rgba(108,99,255,.2); }
.ac-item-name { flex:1;font-weight:500; }
.ac-item-tipo { font-size:.72rem;padding:.1rem .4rem;border-radius:10px;font-weight:700; }
.ac-item-tipo--reventa   { background:rgba(34,211,160,.12);color:#22d3a0; }
.ac-item-tipo--insumo    { background:rgba(251,191,36,.12);color:#fbbf24; }
.ac-item-tipo--elaborado { background:rgba(192,132,252,.12);color:#c084fc; }
.ac-item-price { font-size:.78rem;color:#8990a8; }
.ac-no-results { padding:.6rem .9rem;font-size:.82rem;color:#8990a8;font-style:italic; }
.ac-create-btn { display:flex;align-items:center;gap:.4rem;padding:.55rem .9rem;cursor:pointer;font-size:.84rem;color:#8b85ff;border-top:1px solid rgba(255,255,255,.06);background:rgba(108,99,255,.08);transition:background .12s;font-weight:600; }
.ac-create-btn:hover { background:rgba(108,99,255,.2); }

/* ── SELECTOR DE TIPO INLINE ── */
.item-tipo-select {
    width:100px;flex-shrink:0;
    background:#0f1117;border:1px solid rgba(255,255,255,.12);border-radius:7px;
    padding:.42rem .5rem;color:#e8eaf0;font-size:.78rem;font-weight:600;
    cursor:pointer;outline:none;appearance:none;font-family:inherit;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%238990a8'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:calc(100% - 7px) 50%;
    padding-right:22px;transition:border-color .15s;
}
.item-tipo-select:focus { border-color:#6c63ff; }
.item-tipo-select.tipo-reventa { color:#22d3a0;border-color:rgba(34,211,160,.35); }
.item-tipo-select.tipo-insumo  { color:#fbbf24;border-color:rgba(251,191,36,.35); }

/* ── INLINE CREAR PRODUCTO ── */
.ac-quick-create { background:rgba(108,99,255,.07);border:1px solid rgba(108,99,255,.2);border-radius:8px;padding:.75rem;margin-top:.3rem;animation:pmIn .18s ease; }
.ac-quick-create-title { font-size:.78rem;font-weight:700;color:#8b85ff;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.5rem; }
.ac-quick-create .form-input,.ac-quick-create select { margin-bottom:.4rem;font-size:.82rem; }
.ac-quick-create-row { display:flex;gap:.4rem;margin-top:.2rem; }
.ac-qbtn { display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;border-radius:7px;font-family:inherit;font-size:.78rem;font-weight:600;cursor:pointer;border:none;transition:all .18s; }
.ac-qbtn-confirm { background:linear-gradient(135deg,#6c63ff,#8b85ff);color:#fff; }
.ac-qbtn-cancel  { background:rgba(255,255,255,.06);color:#8990a8;border:1px solid rgba(255,255,255,.1); }

/* ── TIPO BADGE EN ITEMS ── */
.item-tipo-badge { display:inline-flex;align-items:center;gap:.25rem;font-size:.7rem;font-weight:700;padding:.1rem .45rem;border-radius:10px;white-space:nowrap; }
.item-tipo-badge--reventa   { background:rgba(34,211,160,.12);color:#22d3a0; }
.item-tipo-badge--insumo    { background:rgba(251,191,36,.12);color:#fbbf24; }
.item-tipo-badge--elaborado { background:rgba(192,132,252,.12);color:#c084fc; }

/* ── TIPO TOOLTIP ── */
.item-tipo-info { font-size:.72rem;color:#8990a8;font-style:italic;margin-top:.2rem; }
.item-tipo-info--reventa   { color:rgba(34,211,160,.7); }
.item-tipo-info--insumo    { color:rgba(251,191,36,.7); }

/* ── COMPRA CARD MEJORADA ── */
.compra-item-tipo { font-size:.7rem;font-weight:700;padding:.08rem .4rem;border-radius:8px;margin-left:.3rem; }
</style>

{{-- ══════════════════ MODAL: NUEVA COMPRA ══════════════════ --}}
<div class="modal-overlay" id="compraCreateModal">
    <div class="modal" style="max-width: min(780px, 90dvw);">
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
                <div class="items-header" style="font-size:.72rem">
                    <span style="flex:2">Producto</span>
                    <span style="width:100px">Tipo</span>
                    <span style="width:76px;text-align:center">Cant.</span>
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
<div class="modal-overlay" id="compraDeleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Eliminación</h3>
            <button class="modal-close" id="compraDelClose">✕</button>
        </div>
        <form class="modal-body" id="compraDeleteForm">
            <input type="hidden" id="compra-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">¿Eliminar la compra del:</p>
            <p style="color:#ef4444;font-weight:700;font-size:1rem;margin-bottom:8px" id="compra-del-fecha"></p>
            <p style="color:var(--text-primary);font-size:0.9rem;margin-bottom:8px">
                <strong>Productos incluidos:</strong><br>
                <span id="compra-del-productos" style="color:var(--text-secondary)"></span>
            </p>
            <p style="color:rgba(251,191,36,.8);font-size:.78rem;background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:8px;padding:.5rem .75rem;margin-bottom:4px">
                ⚠️ Los ítems de tipo <strong>Reventa</strong> descontarán su cantidad del stock del producto.
            </p>
            <div class="modal-footer" style="margin-top:16px">
                <button type="button" class="btn-cancel" id="compraDelCancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: SINCRONIZAR ══════════════════ --}}
<div class="modal-overlay" id="compraSyncModal">
    <div class="modal" style="max-width: min(500px, 90dvw);">
        <div class="modal-header">
            <h3 class="modal-title">🔄 Sincronizar Compra con Catálogo</h3>
            <button class="modal-close" id="compraSyncClose">✕</button>
        </div>
        <form class="modal-body" id="compraSyncForm">
            <div class="form-group">
                <label class="form-label">Seleccione la compra a sincronizar <span style="color:#ef4444">*</span></label>
                <select class="form-input" id="sync-compra-select" required style="width:100%;background:#0f1117;color:#e8eaf0;border:1px solid rgba(255,255,255,.12);padding:.6rem .75rem;border-radius:8px">
                    <option value="">Cargando compras...</option>
                </select>
            </div>
            
            <div style="color:rgba(108,99,255,.85);font-size:.82rem;background:rgba(108,99,255,.07);border:1px solid rgba(108,99,255,.2);border-radius:8px;padding:.75rem;margin-top:12px;margin-bottom:12px;line-height:1.4">
                ℹ️ <strong>¿Qué hace la sincronización?</strong><br>
                Revisará todos los productos incluidos en la compra elegida:
                <ul style="margin:4px 0 0 16px;padding:0">
                    <li>Si el producto ya existe en el catálogo (coincidencia de nombre al 100%), se sumará la cantidad comprada a su stock actual y se actualizará su precio de compra.</li>
                    <li>Si no existe en el catálogo, se creará un producto nuevo con todos sus datos y stock inicial correspondientes.</li>
                </ul>
                <div style="margin-top:6px;color:#fbbf24;font-weight:600">⚠️ Nota de seguridad: Una vez sincronizada, la compra quedará protegida y no se podrá volver a procesar para evitar duplicados.</div>
            </div>

            <div class="modal-footer" style="margin-top:16px">
                <button type="button" class="btn-cancel" id="compraSyncCancel">Cancelar</button>
                <button type="submit" class="btn-submit" id="compraSyncSubmitBtn">Sincronizar ahora</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ══════════════════════════════════════════════
       ESTADO
    ══════════════════════════════════════════════ */
    let comprasData = [];
    let loaded      = false;
    let itemCount   = 0;

    /* ══════════════════════════════════════════════
       API
    ══════════════════════════════════════════════ */
    async function api(url, method = 'GET', body = null) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const opts  = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res  = await fetch(url, opts);
        const data = await res.json();
        if (!res.ok) throw data;
        return data;
    }

    /* ══════════════════════════════════════════════
       TOAST
    ══════════════════════════════════════════════ */
    function toast(msg, type = 'success') {
        const t = document.getElementById('comprasToast');
        if (!t) return;
        t.textContent = msg;
        t.className   = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ══════════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════════ */
    function formatMoney(v) {
        return '$' + Number(v).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /**
     * Normaliza un string eliminando acentos, mayúsculas y caracteres especiales.
     * Usado para comparación fuzzy sin errores ortográficos.
     */
    function normalize(s) {
        return (s || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')  // elimina acentos
            .replace(/[^a-z0-9 ]/g, ' ')       // reemplaza especiales por espacio
            .replace(/\s+/g, ' ')
            .trim();
    }

    /**
     * Comprueba si `needle` (normalizado) aparece en `haystack` (normalizado).
     * También permite coincidencias parciales token por token.
     */
    function fuzzyMatch(haystack, needle) {
        const hn = normalize(haystack);
        const nn = normalize(needle);
        if (hn.includes(nn)) return true;
        // Token match: cada palabra de la búsqueda debe aparecer en el nombre
        return nn.split(' ').filter(Boolean).every(tok => hn.includes(tok));
    }

    const tipoBadge = {
        reventa:   '🛍️ Reventa',
        insumo:    '🧂 Insumo',
        elaborado: '🍕 Elaborado',
    };
    const tipoInfo = {
        reventa:   '✅ Sumará al stock del producto',
        insumo:    '📋 Solo registra el gasto, sin stock',
        elaborado: null, // los elaborados no se compran, se producen
    };

    /* ══════════════════════════════════════════════
       RENDER TARJETAS
    ══════════════════════════════════════════════ */
    function renderCards(data) {
        const grid       = document.getElementById('comprasGrid');
        const empty      = document.getElementById('comprasEmpty');
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
                    <div class="compra-card-date" style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap">
                        <span class="compra-date-icon">📅</span>
                        <span>${c.fecha}</span>
                        ${c.sincronizado 
                            ? `<span class="compra-item-tipo" style="background:rgba(34,211,160,.12);color:#22d3a0;font-size:0.65rem;font-weight:700;padding:0.1rem 0.4rem;border-radius:8px">🔄 Sincronizada</span>`
                            : `<span class="compra-item-tipo" style="background:rgba(251,191,36,.12);color:#fbbf24;font-size:0.65rem;font-weight:700;padding:0.1rem 0.4rem;border-radius:8px">⚠️ Sin Sincronizar</span>`
                        }
                    </div>
                    <button class="action-btn danger compra-del-btn" title="Eliminar compra" onclick="COMPRA.del(${c.id})">🗑️</button>
                </div>

                ${c.observaciones ? `<p class="compra-obs">${c.observaciones}</p>` : ''}

                <div class="compra-items-list">
                    <div class="compra-items-head">
                        <span class="compra-col-name">Producto</span>
                        <span class="compra-col-qty">Cant.</span>
                        <span class="compra-col-price">P. Unit.</span>
                        <span class="compra-col-sub">Subtotal</span>
                    </div>
                    ${c.items.map(i => {
                        const tipo = i.tipo_producto || 'reventa';
                        const badgeClass = `item-tipo-badge item-tipo-badge--${tipo}`;
                        return `<div class="compra-item-row">
                            <span class="compra-col-name">
                                <span class="compra-item-name" title="${i.producto_nombre}">${i.producto_nombre}</span>
                                <span class="${badgeClass}">${tipoBadge[tipo] || tipo}</span>
                            </span>
                            <span class="compra-col-qty">${i.cantidad % 1 === 0 ? i.cantidad : Number(i.cantidad).toFixed(2)}</span>
                            <span class="compra-col-price">${formatMoney(i.precio_unitario)}</span>
                            <span class="compra-col-sub">${formatMoney(i.subtotal)}</span>
                        </div>`;
                    }).join('')}
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

    /* ══════════════════════════════════════════════
       CARGA
    ══════════════════════════════════════════════ */
    async function load() {
        if (loaded) return;
        try {
            const data = await api('/api/compras');
            comprasData = data.compras || [];
            loaded = true;
            renderCards(comprasData);
        } catch (e) { toast('Error al cargar compras', 'error'); }
    }

    const section = document.querySelector('[data-module-content="compras"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) load();
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
    }

    /* ══════════════════════════════════════════════
       AUTOCOMPLETADO (FUZZY)
    ══════════════════════════════════════════════ */

    /**
     * Cache de productos para autocompletado.
     * Se carga una sola vez desde /panel/api/products/search
     */
    let productsCache = null;
    async function fetchProducts(q) {
        // Primera carga: traer todos y cachear
        if (!productsCache) {
            try {
                const res = await fetch('/panel/api/products/search', {
                    headers: { 'Accept': 'application/json' },
                });
                productsCache = await res.json();
            } catch { productsCache = []; }
        }
        if (!q) return productsCache.slice(0, 10);
        return productsCache.filter(p => fuzzyMatch(p.name, q));
    }

    /**
     * Crea un ítem del formulario de compra con autocompletado fuzzy.
     * @param {object} prefill - Datos iniciales opcionales {name, tipo, price}
     */
    function addItemRow(prefill = {}) {
        itemCount++;
        const cont = document.getElementById('compraItemsContainer');
        if (!cont) return;

        const idx  = itemCount;
        const div  = document.createElement('div');
        div.className  = 'compra-item-row-form';
        div.dataset.idx = idx;

        // Estado interno del ítem
        div._productId  = prefill.product_id || null;
        div._productTipo = prefill.tipo || null;

        div.innerHTML = `
            <div class="ac-wrap" style="flex:2;min-width:0">
                <input class="form-input item-name" placeholder="Buscar o escribir producto..."
                    value="${prefill.name || ''}" autocomplete="off"
                    style="width:100%" data-idx="${idx}">
                <div class="ac-dropdown" id="ac-dd-${idx}" style="display:none"></div>
                <div class="ac-quick-create" id="ac-qc-${idx}" style="display:none"></div>
            </div>
            <select class="item-tipo-select tipo-${prefill.tipo || 'reventa'}" title="Tipo de producto">
                <option value="reventa" ${(prefill.tipo || 'reventa') === 'reventa' ? 'selected' : ''}>🛍️ Reventa</option>
                <option value="insumo"  ${prefill.tipo === 'insumo'  ? 'selected' : ''}>🧂 Insumo</option>
            </select>
            <input class="form-input item-qty" type="number" step="0.01" min="0.01"
                placeholder="Cant." value="${prefill.qty || ''}"
                style="width:72px;text-align:center" required>
            <input class="form-input item-price" type="number" step="0.01" min="0"
                placeholder="$0,00" value="${prefill.price || ''}"
                style="width:96px;text-align:right" required>
            <span class="item-subtotal" style="width:96px;text-align:right;color:var(--blue-light);font-weight:600;font-size:.88rem;align-self:center">$0,00</span>
            <button type="button" class="item-remove-btn" title="Quitar">✕</button>
        `;

        // Tipo select cambia color y actualiza _productTipo
        const tipoSelect = div.querySelector('.item-tipo-select');
        tipoSelect.addEventListener('change', () => {
            tipoSelect.className = 'item-tipo-select tipo-' + tipoSelect.value;
            // Si el tipo cambia manualmente, resetear la selección de producto del catálogo
            div._productId   = null;
            div._productTipo = tipoSelect.value;
            updateItemTipoBadge(div, null);
        });
        div._productTipo = prefill.tipo || 'reventa';

        // Calcular subtotal
        const calcItem = () => {
            const cant  = parseFloat(div.querySelector('.item-qty').value)  || 0;
            const price = parseFloat(div.querySelector('.item-price').value) || 0;
            div.querySelector('.item-subtotal').textContent = formatMoney(cant * price);
            calcModalTotal();
        };
        div.querySelector('.item-qty').addEventListener('input', calcItem);
        div.querySelector('.item-price').addEventListener('input', calcItem);

        // Quitar fila
        div.querySelector('.item-remove-btn').addEventListener('click', () => {
            div.remove();
            calcModalTotal();
        });

        // Autocompletado
        const nameInput = div.querySelector('.item-name');
        const dropdown  = div.querySelector(`#ac-dd-${idx}`);
        let acTimeout;

        nameInput.addEventListener('input', () => {
            clearTimeout(acTimeout);
            // Si cambia el texto, limpiar selección
            div._productId   = null;
            div._productTipo = null;
            updateItemTipoBadge(div, null);

            acTimeout = setTimeout(async () => {
                const q       = nameInput.value.trim();
                const results = await fetchProducts(q);
                renderDropdown(div, idx, dropdown, nameInput, results, q);
            }, 180);
        });

        nameInput.addEventListener('blur', () => {
            // Cerrar con delay para permitir click en dropdown
            setTimeout(() => dropdown.style.display = 'none', 200);
        });
        nameInput.addEventListener('focus', async () => {
            if (nameInput.value.trim().length > 0) {
                const results = await fetchProducts(nameInput.value.trim());
                renderDropdown(div, idx, dropdown, nameInput, results, nameInput.value.trim());
            }
        });

        cont.appendChild(div);
        nameInput.focus();
    }

    /**
     * Actualiza el badge de tipo visible bajo el input del producto.
     */
    function updateItemTipoBadge(rowDiv, tipo) {
        const wrap = rowDiv.querySelector('.ac-wrap');
        // Remover badge existente
        const existing = wrap.querySelector('.item-badge-row');
        if (existing) existing.remove();
        if (!tipo) return;

        const badgeRow = document.createElement('div');
        badgeRow.className = 'item-badge-row';
        badgeRow.style.cssText = 'margin-top:.25rem;display:flex;align-items:center;gap:.4rem';
        badgeRow.innerHTML = `
            <span class="item-tipo-badge item-tipo-badge--${tipo}">${tipoBadge[tipo]}</span>
            ${tipoInfo[tipo] ? `<span class="item-tipo-info item-tipo-info--${tipo}">${tipoInfo[tipo]}</span>` : ''}
        `;
        wrap.insertBefore(badgeRow, wrap.querySelector('.ac-quick-create'));
    }

    /**
     * Renderiza el dropdown de autocompletado.
     */
    function renderDropdown(rowDiv, idx, dropdown, nameInput, results, query) {
        dropdown.innerHTML = '';
        dropdown.style.display = 'block';

        if (!results.length && !query) {
            dropdown.innerHTML = '<div class="ac-no-results">Escribí para buscar...</div>';
        } else if (!results.length) {
            dropdown.innerHTML = `<div class="ac-no-results">Sin coincidencias para "${query}"</div>`;
        } else {
            results.forEach(p => {
                const item = document.createElement('div');
                item.className = 'ac-item';
                item.innerHTML = `
                    <span class="ac-item-name">${p.name}</span>
                    <span class="ac-item-tipo ac-item-tipo--${p.tipo}">${tipoBadge[p.tipo]}</span>
                    ${p.price > 0 ? `<span class="ac-item-price">${formatMoney(p.price)}</span>` : ''}
                `;
                item.addEventListener('mousedown', () => {
                    selectProduct(rowDiv, nameInput, dropdown, p);
                });
                dropdown.appendChild(item);
            });
        }

        // Botón crear si hay texto y no coincide exactamente
        if (query) {
            const exactMatch = results.some(p => normalize(p.name) === normalize(query));
            if (!exactMatch) {
                const createBtn = document.createElement('div');
                createBtn.className = 'ac-create-btn';
                createBtn.innerHTML = `＋ Crear producto "<strong>${query}</strong>"`;
                createBtn.addEventListener('mousedown', () => {
                    dropdown.style.display = 'none';
                    openQuickCreate(rowDiv, idx, query, nameInput);
                });
                dropdown.appendChild(createBtn);
            }
        }
    }

    /**
     * Selecciona un producto del autocompletado.
     */
    function selectProduct(rowDiv, nameInput, dropdown, product) {
        nameInput.value = product.name;
        rowDiv._productId   = product.id;
        rowDiv._productTipo = product.tipo;
        // Sincronizar el select de tipo
        const tipoSel = rowDiv.querySelector('.item-tipo-select');
        if (tipoSel && (product.tipo === 'reventa' || product.tipo === 'insumo')) {
            tipoSel.value = product.tipo;
            tipoSel.className = 'item-tipo-select tipo-' + product.tipo;
        }
        dropdown.style.display = 'none';
        updateItemTipoBadge(rowDiv, null); // badge ya no se usa, lo maneja el select
        // Pre-llenar precio con el último conocido
        const priceInput = rowDiv.querySelector('.item-price');
        if (product.price > 0 && !priceInput.value) {
            priceInput.value = product.price;
            priceInput.dispatchEvent(new Event('input'));
        }
        rowDiv.querySelector('.item-qty')?.focus();
    }

    /**
     * Abre el mini-formulario de creación rápida de producto.
     */
    function openQuickCreate(rowDiv, idx, prefillName, nameInput) {
        const qcEl = rowDiv.querySelector(`#ac-qc-${idx}`);
        if (!qcEl) return;

        qcEl.style.display = 'block';
        qcEl.innerHTML = `
            <div class="ac-quick-create-title">➕ Crear nuevo producto</div>
            <input class="form-input qc-name" placeholder="Nombre" value="${prefillName}" maxlength="255">
            <select class="form-input qc-tipo" style="background:#0f1117;color:#e8eaf0;appearance:none;cursor:pointer">
                <option value="reventa">🛍️ Reventa — para vender tal cual</option>
                <option value="insumo">🧂 Insumo — materia prima</option>
                <option value="elaborado">🍕 Elaborado — producción propia</option>
            </select>
            <input class="form-input qc-desc" placeholder="Descripción (opcional)" maxlength="500">
            <div class="ac-quick-create-row">
                <button type="button" class="ac-qbtn ac-qbtn-confirm">✅ Crear y seleccionar</button>
                <button type="button" class="ac-qbtn ac-qbtn-cancel">Cancelar</button>
            </div>
            <div class="qc-err" style="font-size:.75rem;color:#ff6b6b;margin-top:.3rem;display:none"></div>
        `;

        const qcName    = qcEl.querySelector('.qc-name');
        const qcTipo    = qcEl.querySelector('.qc-tipo');
        const qcDesc    = qcEl.querySelector('.qc-desc');
        const qcConfirm = qcEl.querySelector('.ac-qbtn-confirm');
        const qcCancel  = qcEl.querySelector('.ac-qbtn-cancel');
        const qcErr     = qcEl.querySelector('.qc-err');

        qcCancel.addEventListener('click', () => {
            qcEl.style.display = 'none';
        });

        qcConfirm.addEventListener('click', async () => {
            const name = qcName.value.trim();
            const tipo = qcTipo.value;
            const desc = qcDesc.value.trim();

            if (!name) { qcErr.textContent = 'El nombre es requerido.'; qcErr.style.display = 'block'; return; }

            qcConfirm.disabled = true;
            qcConfirm.textContent = 'Creando…';
            qcErr.style.display = 'none';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const res   = await fetch('/panel/api/products', {
                    method:  'POST',
                    headers: {
                        'Accept':       'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ name, tipo, description: desc || null }),
                });
                const data = await res.json();
                if (!res.ok) throw data;

                // Invalidar cache para que aparezca en próximas búsquedas
                productsCache = null;

                // Seleccionar el producto recién creado
                qcEl.style.display = 'none';
                nameInput.value = data.product.name;
                rowDiv._productId   = data.product.id;
                rowDiv._productTipo = data.product.tipo;
                updateItemTipoBadge(rowDiv, data.product.tipo);
                rowDiv.querySelector('.item-qty')?.focus();
                toast('Producto "' + data.product.name + '" creado.', 'success');

            } catch (err) {
                qcErr.textContent  = err.message || Object.values(err.errors || {}).flat()[0] || 'Error al crear';
                qcErr.style.display = 'block';
                qcConfirm.disabled  = false;
                qcConfirm.textContent = '✅ Crear y seleccionar';
            }
        });
    }

    /* ══════════════════════════════════════════════
       TOTAL DEL MODAL
    ══════════════════════════════════════════════ */
    function calcModalTotal() {
        let total = 0;
        document.querySelectorAll('.compra-item-row-form').forEach(row => {
            const cant  = parseFloat(row.querySelector('.item-qty').value)  || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            total += cant * price;
        });
        const totalEl = document.getElementById('compraModalTotal');
        if (totalEl) totalEl.textContent = formatMoney(total);
    }

    /* ══════════════════════════════════════════════
       ABRIR / SUBMIT NUEVA COMPRA
    ══════════════════════════════════════════════ */
    document.getElementById('btnAddItem')?.addEventListener('click', () => addItemRow());

    document.getElementById('btnNewCompra')?.addEventListener('click', () => {
        document.getElementById('compraCreateForm')?.reset();
        document.getElementById('compraItemsContainer').innerHTML = '';
        itemCount = 0;
        const today = new Date().toISOString().slice(0, 10);
        const fechaInput = document.getElementById('compra-fecha');
        if (fechaInput) fechaInput.value = today;
        document.getElementById('compraModalTotal').textContent = '$0,00';
        addItemRow();
        window.openModal('compraCreateModal');
    });

    document.getElementById('compraCreateForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const fd    = new FormData(e.target);
        const items = [];

        document.querySelectorAll('.compra-item-row-form').forEach(row => {
            const nameVal  = row.querySelector('.item-name').value.trim();
            const tipoSel  = row.querySelector('.item-tipo-select');
            const tipo     = row._productTipo || (tipoSel ? tipoSel.value : 'reventa');
            const pid      = row._productId   || null;
            if (!nameVal) return;
            items.push({
                product_id:      pid,
                producto_nombre: nameVal,
                tipo_producto:   tipo,
                cantidad:        parseFloat(row.querySelector('.item-qty').value),
                precio_unitario: parseFloat(row.querySelector('.item-price').value),
            });
        });

        if (!items.length) { toast('Agregá al menos un producto', 'error'); return; }

        // Validación: sin tipo asignado = advertencia
        const sinTipo = items.filter(i => !i.product_id && i.tipo_producto === 'reventa');
        // Nota: si no seleccionaron del catálogo, igual se envía como reventa por defecto

        const body = { fecha: fd.get('fecha'), observaciones: fd.get('observaciones') || null, items };

        try {
            const data = await api('/api/compras', 'POST', body);
            toast(data.message);
            window.closeModal('compraCreateModal');
            comprasData.unshift(data.compra);
            renderCards(comprasData);
            // Refrescar cache de productos (stock actualizado)
            productsCache = null;
            // Refrescar la página de forma automática para actualizar stock en todos los módulos
            setTimeout(() => window.location.reload(), 1200);
        } catch (err) {
            toast(err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al registrar', 'error');
        }
    });

    /* ══════════════════════════════════════════════
       DELETE
    ══════════════════════════════════════════════ */
    window.COMPRA = window.COMPRA || {};
    COMPRA.del = function (id) {
        const c = comprasData.find(x => x.id === id);
        if (!c) return;
        document.getElementById('compra-del-id').value = id;
        document.getElementById('compra-del-fecha').textContent = c.fecha;

        const prodNames = c.items.map(i => {
            const tipo = i.tipo_producto || 'reventa';
            return `${i.producto_nombre} (${tipoBadge[tipo]})`;
        }).join(', ');
        const elProductos = document.getElementById('compra-del-productos');
        if (elProductos) elProductos.textContent = prodNames || 'Ninguno';

        window.openModal('compraDeleteModal');
    };

    document.getElementById('compraDeleteForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const id = document.getElementById('compra-del-id').value;
        try {
            const data = await api(`/api/compras/${id}`, 'DELETE');
            toast(data.message);
            window.closeModal('compraDeleteModal');
            comprasData = comprasData.filter(x => x.id != id);
            renderCards(comprasData);
            // Refrescar cache de productos (stock revertido)
            productsCache = null;
            // Refrescar la página de forma automática para actualizar stock en todos los módulos
            setTimeout(() => window.location.reload(), 1200);
        } catch (err) { toast(err.message || 'Error al eliminar', 'error'); }
    });

    /* ══════════════════════════════════════════════
       SINCRONIZAR
    ══════════════════════════════════════════════ */
    const syncModal      = document.getElementById('compraSyncModal');
    const syncSelect     = document.getElementById('sync-compra-select');
    const syncForm       = document.getElementById('compraSyncForm');
    const syncSubmitBtn  = document.getElementById('compraSyncSubmitBtn');

    document.getElementById('btnSyncCompra')?.addEventListener('click', () => {
        // Filtrar solo las compras que no han sido sincronizadas
        const unsynced = comprasData.filter(c => !c.sincronizado);
        
        if (syncSelect) {
            syncSelect.innerHTML = '';
            if (unsynced.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'No hay compras históricas sin sincronizar';
                syncSelect.appendChild(opt);
                if (syncSubmitBtn) syncSubmitBtn.disabled = true;
            } else {
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Seleccione una compra --';
                syncSelect.appendChild(defaultOpt);

                unsynced.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    const itemsText = c.items.map(i => `${i.producto_nombre} (x${i.cantidad})`).join(', ');
                    opt.textContent = `Compra #${c.id} - ${c.fecha} (${formatMoney(c.total)}) [${itemsText.substring(0, 50)}${itemsText.length > 50 ? '...' : ''}]`;
                    syncSelect.appendChild(opt);
                });
                if (syncSubmitBtn) syncSubmitBtn.disabled = false;
            }
        }
        
        window.openModal('compraSyncModal');
    });

    const closeSyncModal = () => window.closeModal('compraSyncModal');

    document.getElementById('compraSyncClose')?.addEventListener('click', closeSyncModal);
    document.getElementById('compraSyncCancel')?.addEventListener('click', closeSyncModal);

    syncForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const compraId = syncSelect.value;
        if (!compraId) {
            toast('Por favor, seleccione una compra válida', 'error');
            return;
        }

        if (syncSubmitBtn) {
            syncSubmitBtn.disabled = true;
            syncSubmitBtn.textContent = 'Sincronizando...';
        }

        try {
            const data = await api('/api/compras/sincronizar', 'POST', { compra_id: parseInt(compraId, 10) });
            toast(data.message);
            closeSyncModal();
            
            // Actualizar la compra en comprasData
            const updatedCompra = data.compra;
            comprasData = comprasData.map(c => c.id === updatedCompra.id ? updatedCompra : c);
            renderCards(comprasData);
            
            // Forzar recarga del catálogo de productos y autocompletado
            productsCache = null;
            // Refrescar la página de forma automática para actualizar stock en todos los módulos
            setTimeout(() => window.location.reload(), 1200);
        } catch (err) {
            toast(err.message || 'Error al sincronizar la compra', 'error');
        } finally {
            if (syncSubmitBtn) {
                syncSubmitBtn.disabled = false;
                syncSubmitBtn.textContent = 'Sincronizar ahora';
            }
        }
    });

})();
</script>
