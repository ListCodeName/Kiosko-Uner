/**
 * KIOSKO-UNER | MÓDULO KIOSCO – Punto de Venta (POS)
 * Catálogo de productos + carrito interactivo + checkout con método de pago y estado
 *
 * Las ventas se persisten en localStorage['kiosko_ventas'] (compartido con ventas.blade.php).
 * Al confirmar una venta:
 *   1. Se abre el modal de checkout para elegir método y estado de pago.
 *   2. Se guarda la venta en localStorage.
 *   3. Se notifica al módulo de ventas (si está montado) mediante window.VentasModuleRefresh().
 *   4. Se llama a la API para descontar stock (/panel/api/kiosco/sale).
 */
(function () {
    'use strict';

    /* ──────────────────────────────────────────────────────────
       CONSTANTES
    ────────────────────────────────────────────────────────── */
    const API_CATEGORIES = '/panel/api/kiosco/categories';
    const API_SALE       = '/panel/api/kiosco/sale';
    const STORAGE_KEY    = 'kiosko_ventas';

    /* ──────────────────────────────────────────────────────────
       ESTADO LOCAL
    ────────────────────────────────────────────────────────── */
    const state = {
        categories:  [],
        allProducts: [],
        activeCat:   'all',
        searchQuery: '',
        cart:        [],  // [{product_id, name, price, qty, stock}]
        submitting:  false,
    };

    // Estado del checkout
    const checkout = {
        metodo: 'efectivo',   // 'efectivo' | 'transferencia'
        estado: 'pagado',     // 'pagado'   | 'pendiente'
    };

    /* ──────────────────────────────────────────────────────────
       HELPERS
    ────────────────────────────────────────────────────────── */
    function getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }
    function jsonHeaders() {
        return {
            'Accept':        'application/json',
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  getToken(),
        };
    }
    function formatPrice(n) {
        return '$' + Number(n).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }
    function formatMoney(v) {
        return '$' + Number(v).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function today() {
        return new Date().toISOString().slice(0, 10);
    }
    function nowTime() {
        return new Date().toTimeString().slice(0, 5);
    }

    /* ──────────────────────────────────────────────────────────
       PERSISTENCIA LOCAL DE VENTAS
    ────────────────────────────────────────────────────────── */
    function storageLoad() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) { return []; }
    }

    function storageSave(ventas) {
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(ventas)); } catch (e) {}
    }

    function nextVentaId(ventas) {
        if (!ventas.length) return 1;
        return Math.max(...ventas.map(v => v.id)) + 1;
    }

    /* ──────────────────────────────────────────────────────────
       TOAST
    ────────────────────────────────────────────────────────── */
    function showToast(msg, type = 'success') {
        const container = document.getElementById('posToastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `pos-toast ${type}`;
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(10px)'; }, 2500);
        setTimeout(() => toast.remove(), 3000);
    }

    /* ──────────────────────────────────────────────────────────
       CART TOGGLE
    ────────────────────────────────────────────────────────── */
    function openCart()   { document.getElementById('posLayout')?.classList.add('cart-open');    }
    function closeCart()  { document.getElementById('posLayout')?.classList.remove('cart-open'); }
    function toggleCart() { document.getElementById('posLayout')?.classList.toggle('cart-open'); }

    function updateBadge() {
        const badge    = document.getElementById('posCartBadge');
        if (!badge) return;
        const totalQty = state.cart.reduce((sum, c) => sum + c.qty, 0);
        badge.textContent   = totalQty;
        badge.style.display = totalQty > 0 ? 'flex' : 'none';
    }

    /* ──────────────────────────────────────────────────────────
       CARGA DE DATOS
    ────────────────────────────────────────────────────────── */
    async function loadCategories() {
        const grid = document.getElementById('posProductGrid');
        if (!grid) return;
        grid.innerHTML = `<div class="pos-loading"><div class="pos-spinner"></div><span>Cargando productos...</span></div>`;

        try {
            const res = await fetch(API_CATEGORIES, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            state.categories = await res.json();

            state.allProducts = [];
            state.categories.forEach(cat => {
                cat.products.forEach(p => {
                    state.allProducts.push({
                        ...p,
                        category_id:   cat.id,
                        category_name: cat.name,
                        category_icon: cat.icon,
                        is_produced:   cat.is_produced,
                    });
                });
            });

            renderCategoryChips();
            renderProducts();
        } catch {
            grid.innerHTML = `<div class="pos-no-results">⚠️ Error al cargar los productos</div>`;
        }
    }

    /* ──────────────────────────────────────────────────────────
       RENDER: CHIPS DE CATEGORÍA
    ────────────────────────────────────────────────────────── */
    function renderCategoryChips() {
        const bar = document.getElementById('posCategoryBar');
        if (!bar) return;

        let html = `<button class="pos-cat-chip active" data-cat-id="all">
                        <span class="pos-cat-chip-icon">📋</span><span>Todos</span>
                    </button>`;

        state.categories.forEach(cat => {
            const producedClass = cat.is_produced ? ' produced' : '';
            html += `<button class="pos-cat-chip${producedClass}" data-cat-id="${cat.id}">
                         <span class="pos-cat-chip-icon">${cat.icon || '📦'}</span>
                         <span>${cat.name} (${cat.products.length})</span>
                     </button>`;
        });

        bar.innerHTML = html;
    }

    /* ──────────────────────────────────────────────────────────
       RENDER: GRID DE PRODUCTOS
    ────────────────────────────────────────────────────────── */
    function renderProducts() {
        const grid = document.getElementById('posProductGrid');
        if (!grid) return;

        let filtered = state.allProducts;

        if (state.activeCat !== 'all') {
            const catId = parseInt(state.activeCat, 10);
            filtered = filtered.filter(p => p.category_id === catId);
        }
        if (state.searchQuery) {
            const q = state.searchQuery.toLowerCase();
            filtered = filtered.filter(p => p.name.toLowerCase().includes(q));
        }

        if (!filtered.length) {
            grid.innerHTML = `<div class="pos-no-results"><div style="font-size:2rem;margin-bottom:8px">🔍</div>No se encontraron productos</div>`;
            return;
        }

        grid.innerHTML = filtered.map(p => {
            const outClass  = p.stock <= 0 ? ' out-of-stock' : '';
            const stockClass = p.stock <= 0 ? 'empty' : p.stock <= 5 ? 'low' : '';
            const stockText  = p.stock <= 0 ? 'Sin stock' : `${p.stock} disponibles`;
            const badge      = p.is_produced ? `<span class="pos-product-badge">Elaborado</span>` : '';
            return `<div class="pos-product-card${outClass}" data-product-id="${p.id}">
                        ${badge}
                        <div class="pos-product-name" title="${p.name}">${p.name}</div>
                        <div class="pos-product-price">${formatPrice(p.price)}</div>
                        <div class="pos-product-stock ${stockClass}">${stockText}</div>
                        <button class="pos-product-add" data-add-id="${p.id}">＋ Agregar</button>
                    </div>`;
        }).join('');
    }

    /* ──────────────────────────────────────────────────────────
       CARRITO
    ────────────────────────────────────────────────────────── */
    function addToCart(productId) {
        const product  = state.allProducts.find(p => p.id === productId);
        if (!product || product.stock <= 0) return;

        const existing = state.cart.find(c => c.product_id === productId);
        if (existing) {
            if (existing.qty >= product.stock) {
                showToast('Stock máximo alcanzado', 'error');
                return;
            }
            existing.qty++;
        } else {
            state.cart.push({
                product_id: product.id,
                name:       product.name,
                price:      product.price,
                qty:        1,
                stock:      product.stock,
            });
        }

        renderCart();
        updateBadge();
        openCart();
        showToast(`${product.name} agregado`, 'success');
    }

    function updateQty(productId, delta) {
        const item = state.cart.find(c => c.product_id === productId);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) {
            state.cart = state.cart.filter(c => c.product_id !== productId);
        } else if (item.qty > item.stock) {
            item.qty = item.stock;
            showToast('Stock máximo alcanzado', 'error');
        }
        renderCart();
        updateBadge();
    }

    function clearCart() {
        state.cart = [];
        renderCart();
        updateBadge();
    }

    function renderCart() {
        const itemsEl  = document.getElementById('posCartItems');
        const emptyEl  = document.getElementById('posCartEmpty');
        const footerEl = document.getElementById('posCartFooter');
        const clearBtn = document.getElementById('posCartClear');
        const countEl  = document.getElementById('posCartCount');
        const totalEl  = document.getElementById('posCartTotal');

        if (state.cart.length === 0) {
            emptyEl.style.display  = 'flex';
            footerEl.style.display = 'none';
            clearBtn.style.display = 'none';
            itemsEl.querySelectorAll('.pos-cart-item').forEach(el => el.remove());
            return;
        }

        emptyEl.style.display  = 'none';
        footerEl.style.display = 'block';
        clearBtn.style.display = 'inline-flex';

        let total = 0, itemCount = 0;

        const itemsHtml = state.cart.map(item => {
            const subtotal = item.price * item.qty;
            total     += subtotal;
            itemCount += item.qty;
            return `<div class="pos-cart-item" data-cart-product="${item.product_id}">
                        <div class="pos-cart-item-info">
                            <div class="pos-cart-item-name" title="${item.name}">${item.name}</div>
                            <div class="pos-cart-item-price">${formatPrice(item.price)} c/u</div>
                        </div>
                        <div class="pos-cart-item-qty">
                            <button class="pos-cart-qty-btn${item.qty <= 1 ? ' remove' : ''}" data-qty-delta="-1" data-qty-product="${item.product_id}">
                                ${item.qty <= 1 ? '✕' : '−'}
                            </button>
                            <span class="pos-cart-qty-val">${item.qty}</span>
                            <button class="pos-cart-qty-btn" data-qty-delta="1" data-qty-product="${item.product_id}">＋</button>
                        </div>
                        <div class="pos-cart-item-subtotal">${formatPrice(subtotal)}</div>
                    </div>`;
        }).join('');

        itemsEl.querySelectorAll('.pos-cart-item').forEach(el => el.remove());
        emptyEl.insertAdjacentHTML('afterend', itemsHtml);

        countEl.textContent = `${itemCount} unidad${itemCount !== 1 ? 'es' : ''}`;
        totalEl.textContent = formatPrice(total);
    }

    /* ──────────────────────────────────────────────────────────
       CHECKOUT: ABRIR MODAL
    ────────────────────────────────────────────────────────── */
    function openCheckout() {
        if (state.cart.length === 0) return;

        // Calcular resumen
        const total      = state.cart.reduce((s, c) => s + c.price * c.qty, 0);
        const unidades   = state.cart.reduce((s, c) => s + c.qty, 0);
        const productos  = state.cart.length;

        document.getElementById('chkCount').textContent  = `${unidades} unidad${unidades !== 1 ? 'es' : ''} (${productos} tipo${productos !== 1 ? 's' : ''})`;
        document.getElementById('chkTotal').textContent  = formatMoney(total);
        document.getElementById('chkCliente').value = '';
        document.getElementById('chkObs').value     = '';

        // Reset selecciones
        setCheckoutMetodo('efectivo');
        setCheckoutEstado('pagado');

        window.openModal('posCheckoutModal');
    }

    function setCheckoutMetodo(metodo) {
        checkout.metodo = metodo;
        document.querySelectorAll('.pos-pay-method').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.method === metodo);
        });
    }

    function setCheckoutEstado(estado) {
        checkout.estado = estado;
        document.querySelectorAll('.pos-pay-state').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.state === estado);
        });
    }

    /* ──────────────────────────────────────────────────────────
       CHECKOUT: CONFIRMAR VENTA
    ────────────────────────────────────────────────────────── */
    async function confirmSale() {
        if (state.cart.length === 0 || state.submitting) return;

        const btn    = document.getElementById('chkBtnConfirm');
        const total  = state.cart.reduce((s, c) => s + c.price * c.qty, 0);
        const items  = state.cart.map(c => ({
            product_id: c.product_id,
            qty:        c.qty,
            nombre:     c.name,
            precio:     c.price,
            cantidad:   c.qty,
        }));

        state.submitting = true;
        btn.disabled = true;
        btn.innerHTML = '<div class="pos-spinner" style="width:16px;height:16px;border-width:2px"></div> Registrando…';

        /* 1. Guardar en localStorage (fuente de verdad local) */
        const ventas   = storageLoad();
        const newVenta = {
            id:      nextVentaId(ventas),
            fecha:   today(),
            hora:    nowTime(),
            cliente: document.getElementById('chkCliente').value.trim(),
            metodo:  checkout.metodo,
            estado:  checkout.estado,
            obs:     document.getElementById('chkObs').value.trim(),
            total,
            items,
        };
        ventas.unshift(newVenta);
        storageSave(ventas);

        /* 2. Notificar al módulo de ventas (si está montado) */
        if (typeof window.VentasModuleRefresh === 'function') {
            window.VentasModuleRefresh();
        }

        /* 3. Llamar a la API para descontar stock (best-effort) */
        try {
            const res = await fetch(API_SALE, {
                method:  'POST',
                headers: jsonHeaders(),
                body:    JSON.stringify({
                    items:  items.map(i => ({ product_id: i.product_id, qty: i.qty })),
                    metodo: checkout.metodo,
                    estado: checkout.estado,
                }),
            });

            const data = await res.json();
            if (!res.ok) {
                showToast(data.message || 'Stock no pudo descontarse en servidor', 'error');
            } else {
                const label = checkout.estado === 'pendiente' ? '🕐 Venta pendiente' : '✅ Venta registrada';
                showToast(`${label} — ${formatMoney(total)}`, 'success');
            }
        } catch {
            showToast('⚠️ Venta guardada localmente (sin conexión al servidor)', 'error');
        }

        /* 4. Limpiar carrito y recargar catálogo (stock actualizado) */
        state.cart = [];
        renderCart();
        updateBadge();
        closeCart();
        window.closeModal('posCheckoutModal');
        await loadCategories();

        state.submitting = false;
        btn.disabled     = false;
        btn.innerHTML    = '<span id="chkBtnIcon">💳</span> Registrar Venta';
    }

    /* ──────────────────────────────────────────────────────────
       API PÚBLICA: restaurar stock al devolver una venta
       Llamada desde ventas.blade.php → window.KioscoRestoreStock(items)
    ────────────────────────────────────────────────────────── */
    window.KioscoRestoreStock = function (items) {
        if (!items || !items.length) return;

        // Actualizar stock en memoria
        items.forEach(item => {
            const p = state.allProducts.find(x => x.id === item.product_id);
            if (p) p.stock = (p.stock || 0) + item.cantidad;
        });

        // Re-renderizar grid con stock actualizado
        renderProducts();

        // Llamar a la API para reponer en servidor (best-effort)
        fetch('/panel/api/kiosco/restore-stock', {
            method:  'POST',
            headers: jsonHeaders(),
            body:    JSON.stringify({ items: items.map(i => ({ product_id: i.product_id, qty: i.cantidad })) }),
        }).catch(() => {}); // silencioso si no existe el endpoint aún
    };

    /* ──────────────────────────────────────────────────────────
       INIT
    ────────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {

        const kioscoSection = document.querySelector('[data-module-content="kiosco"]');
        let loaded = false;

        function ensureLoaded() {
            if (!loaded) { loaded = true; loadCategories(); }
        }

        const observer = new MutationObserver(() => {
            if (kioscoSection?.classList.contains('active')) ensureLoaded();
        });
        if (kioscoSection) {
            observer.observe(kioscoSection, { attributes: true, attributeFilter: ['class'] });
            if (kioscoSection.classList.contains('active')) ensureLoaded();
        }

        document.querySelectorAll('.nav-item[data-module="kiosco"]').forEach(btn => {
            btn.addEventListener('click', () => ensureLoaded());
        });

        /* ── Chips de categoría ── */
        const catBar = document.getElementById('posCategoryBar');
        if (catBar) {
            catBar.addEventListener('click', e => {
                const chip = e.target.closest('.pos-cat-chip');
                if (!chip) return;
                catBar.querySelectorAll('.pos-cat-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                state.activeCat = chip.dataset.catId;
                renderProducts();
            });
        }

        /* ── Búsqueda ── */
        const searchInput = document.getElementById('posSearch');
        if (searchInput) {
            let debounce;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounce);
                debounce = setTimeout(() => {
                    state.searchQuery = searchInput.value.trim();
                    renderProducts();
                }, 200);
            });
        }

        /* ── Agregar al carrito desde grid ── */
        const grid = document.getElementById('posProductGrid');
        if (grid) {
            grid.addEventListener('click', e => {
                const addBtn = e.target.closest('[data-add-id]');
                const card   = e.target.closest('.pos-product-card');
                if (addBtn) addToCart(parseInt(addBtn.dataset.addId, 10));
                else if (card && !e.target.closest('button')) addToCart(parseInt(card.dataset.productId, 10));
            });
        }

        /* ── Controles de qty en carrito ── */
        const cartItems = document.getElementById('posCartItems');
        if (cartItems) {
            cartItems.addEventListener('click', e => {
                const qtyBtn = e.target.closest('[data-qty-delta]');
                if (!qtyBtn) return;
                updateQty(parseInt(qtyBtn.dataset.qtyProduct, 10), parseInt(qtyBtn.dataset.qtyDelta, 10));
            });
        }

        /* ── Vaciar carrito ── */
        document.getElementById('posCartClear')?.addEventListener('click', clearCart);

        /* ── Confirmar venta → abrir modal checkout ── */
        document.getElementById('posBtnConfirm')?.addEventListener('click', openCheckout);

        /* ── Toggle / close carrito ── */
        document.getElementById('posCartToggle')?.addEventListener('click', toggleCart);
        document.getElementById('posCartClose')?.addEventListener('click', closeCart);

        /* ── Checkout: selección de método de pago ── */
        document.querySelectorAll('.pos-pay-method').forEach(btn => {
            btn.addEventListener('click', () => setCheckoutMetodo(btn.dataset.method));
        });

        /* ── Checkout: selección de estado de pago ── */
        document.querySelectorAll('.pos-pay-state').forEach(btn => {
            btn.addEventListener('click', () => setCheckoutEstado(btn.dataset.state));
        });

        /* ── Checkout: cancelar ── */
        document.getElementById('chkBtnCancel')?.addEventListener('click', () => {
            window.closeModal('posCheckoutModal');
        });

        /* ── Checkout: confirmar ── */
        document.getElementById('chkBtnConfirm')?.addEventListener('click', confirmSale);

    });

})();
