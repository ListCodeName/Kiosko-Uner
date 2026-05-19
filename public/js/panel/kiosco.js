/**
 * KIOSKO-UNER | MÓDULO KIOSCO – Punto de Venta (POS)
 * Catálogo de productos con filtrado por categoría/búsqueda + carrito interactivo
 */
(function () {
    'use strict';

    /* ── URLs de API ────────────────────────────────────────── */
    const API_CATEGORIES = '/panel/api/kiosco/categories';
    const API_SALE       = '/panel/api/kiosco/sale';

    /* ── Estado local ───────────────────────────────────────── */
    const state = {
        categories:   [],    // [{id, name, icon, is_produced, products:[…]}]
        allProducts:  [],    // flat list de todos los productos
        activeCat:    'all', // 'all' | id numérico
        searchQuery:  '',
        cart:         [],    // [{product_id, name, price, qty, stock}]
        submitting:   false,
    };

    /* ── Helpers ────────────────────────────────────────────── */
    function getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }
    function jsonHeaders() {
        return { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getToken() };
    }
    function formatPrice(n) {
        return '$' + Number(n).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    /* ── Toast ──────────────────────────────────────────────── */
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

    /* ── Cart toggle ────────────────────────────────────────── */
    function openCart() {
        const layout = document.getElementById('posLayout');
        if (layout) layout.classList.add('cart-open');
    }
    function closeCart() {
        const layout = document.getElementById('posLayout');
        if (layout) layout.classList.remove('cart-open');
    }
    function toggleCart() {
        const layout = document.getElementById('posLayout');
        if (layout) layout.classList.toggle('cart-open');
    }
    function updateBadge() {
        const badge = document.getElementById('posCartBadge');
        if (!badge) return;
        const totalQty = state.cart.reduce((sum, c) => sum + c.qty, 0);
        if (totalQty > 0) {
            badge.textContent = totalQty;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    /* ══════════════════════════════════════════════════════════
     * CARGA DE DATOS
     * ══════════════════════════════════════════════════════════ */
    async function loadCategories() {
        const grid = document.getElementById('posProductGrid');
        if (!grid) return;

        grid.innerHTML = `<div class="pos-loading"><div class="pos-spinner"></div><span>Cargando productos...</span></div>`;

        try {
            const res = await fetch(API_CATEGORIES, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            state.categories = await res.json();

            // Flatten products
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

    /* ══════════════════════════════════════════════════════════
     * RENDER: CHIPS DE CATEGORÍA
     * ══════════════════════════════════════════════════════════ */
    function renderCategoryChips() {
        const bar = document.getElementById('posCategoryBar');
        if (!bar) return;

        let html = `<button class="pos-cat-chip active" data-cat-id="all">
                        <span class="pos-cat-chip-icon">📋</span><span>Todos</span>
                    </button>`;

        state.categories.forEach(cat => {
            const producedClass = cat.is_produced ? ' produced' : '';
            const count = cat.products.length;
            html += `<button class="pos-cat-chip${producedClass}" data-cat-id="${cat.id}">
                        <span class="pos-cat-chip-icon">${cat.icon || '📦'}</span>
                        <span>${cat.name} (${count})</span>
                     </button>`;
        });

        bar.innerHTML = html;
    }

    /* ══════════════════════════════════════════════════════════
     * RENDER: GRID DE PRODUCTOS
     * ══════════════════════════════════════════════════════════ */
    function renderProducts() {
        const grid = document.getElementById('posProductGrid');
        if (!grid) return;

        let filtered = state.allProducts;

        // Filtrar por categoría
        if (state.activeCat !== 'all') {
            const catId = parseInt(state.activeCat, 10);
            filtered = filtered.filter(p => p.category_id === catId);
        }

        // Filtrar por búsqueda
        if (state.searchQuery) {
            const q = state.searchQuery.toLowerCase();
            filtered = filtered.filter(p => p.name.toLowerCase().includes(q));
        }

        if (filtered.length === 0) {
            grid.innerHTML = `<div class="pos-no-results">
                <div style="font-size:2rem;margin-bottom:8px">🔍</div>
                No se encontraron productos
            </div>`;
            return;
        }

        grid.innerHTML = filtered.map(p => {
            const outClass = p.stock <= 0 ? ' out-of-stock' : '';
            const stockClass = p.stock <= 0 ? 'empty' : p.stock <= 5 ? 'low' : '';
            const stockText = p.stock <= 0 ? 'Sin stock' : `${p.stock} disponibles`;
            const badge = p.is_produced ? `<span class="pos-product-badge">Elaborado</span>` : '';

            return `<div class="pos-product-card${outClass}" data-product-id="${p.id}">
                        ${badge}
                        <div class="pos-product-name" title="${p.name}">${p.name}</div>
                        <div class="pos-product-price">${formatPrice(p.price)}</div>
                        <div class="pos-product-stock ${stockClass}">${stockText}</div>
                        <button class="pos-product-add" data-add-id="${p.id}">＋ Agregar</button>
                    </div>`;
        }).join('');
    }

    /* ══════════════════════════════════════════════════════════
     * CARRITO
     * ══════════════════════════════════════════════════════════ */
    function addToCart(productId) {
        const product = state.allProducts.find(p => p.id === productId);
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
        const itemsEl   = document.getElementById('posCartItems');
        const emptyEl   = document.getElementById('posCartEmpty');
        const footerEl  = document.getElementById('posCartFooter');
        const clearBtn  = document.getElementById('posCartClear');
        const countEl   = document.getElementById('posCartCount');
        const totalEl   = document.getElementById('posCartTotal');

        if (state.cart.length === 0) {
            // Mostrar estado vacío
            emptyEl.style.display   = 'flex';
            footerEl.style.display  = 'none';
            clearBtn.style.display  = 'none';
            // Limpiar items (mantener el empty div)
            const items = itemsEl.querySelectorAll('.pos-cart-item');
            items.forEach(el => el.remove());
            return;
        }

        emptyEl.style.display  = 'none';
        footerEl.style.display = 'block';
        clearBtn.style.display = 'inline-flex';

        let total     = 0;
        let itemCount = 0;

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

        // Preservar el empty div, reemplazar items
        const existingItems = itemsEl.querySelectorAll('.pos-cart-item');
        existingItems.forEach(el => el.remove());
        emptyEl.insertAdjacentHTML('afterend', itemsHtml);

        countEl.textContent = `${itemCount} unidad${itemCount !== 1 ? 'es' : ''}`;
        totalEl.textContent = formatPrice(total);
    }

    /* ══════════════════════════════════════════════════════════
     * CONFIRMAR VENTA
     * ══════════════════════════════════════════════════════════ */
    async function confirmSale() {
        if (state.cart.length === 0 || state.submitting) return;

        const btn = document.getElementById('posBtnConfirm');
        state.submitting = true;
        btn.disabled = true;
        btn.innerHTML = '<div class="pos-spinner" style="width:18px;height:18px;border-width:2px"></div> Procesando...';

        try {
            const items = state.cart.map(c => ({ product_id: c.product_id, qty: c.qty }));

            const res = await fetch(API_SALE, {
                method: 'POST',
                headers: jsonHeaders(),
                body: JSON.stringify({ items }),
            });

            const data = await res.json();

            if (!res.ok) {
                showToast(data.message || 'Error al procesar la venta', 'error');
                return;
            }

            showToast(`✅ Venta #${data.sale_id} registrada — ${formatPrice(data.total)}`, 'success');

            // Limpiar carrito y recargar productos (stock actualizado)
            state.cart = [];
            renderCart();
            updateBadge();
            closeCart();
            await loadCategories();

        } catch {
            showToast('Error de conexión al registrar la venta', 'error');
        } finally {
            state.submitting = false;
            btn.disabled = false;
            btn.innerHTML = '<span class="pos-btn-confirm-icon">💳</span> Confirmar Venta';
        }
    }

    /* ══════════════════════════════════════════════════════════
     * INIT
     * ══════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {

        // Cargar al init si el módulo está visible, o al activar
        const kioscoSection = document.querySelector('[data-module-content="kiosco"]');
        let loaded = false;

        function ensureLoaded() {
            if (!loaded) {
                loaded = true;
                loadCategories();
            }
        }

        // Observar cuando el módulo se activa
        const observer = new MutationObserver(() => {
            if (kioscoSection && kioscoSection.classList.contains('active')) {
                ensureLoaded();
            }
        });
        if (kioscoSection) {
            observer.observe(kioscoSection, { attributes: true, attributeFilter: ['class'] });
            if (kioscoSection.classList.contains('active')) ensureLoaded();
        }

        // También disparar en click del sidebar
        document.querySelectorAll('.nav-item[data-module="kiosco"]').forEach(btn => {
            btn.addEventListener('click', () => ensureLoaded());
        });

        // ── Delegación de eventos: chips de categoría ────────
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

        // ── Delegación: búsqueda ────────────────────────────
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

        // ── Delegación: agregar al carrito desde grid ────────
        const grid = document.getElementById('posProductGrid');
        if (grid) {
            grid.addEventListener('click', e => {
                const addBtn = e.target.closest('[data-add-id]');
                const card   = e.target.closest('.pos-product-card');
                if (addBtn) {
                    addToCart(parseInt(addBtn.dataset.addId, 10));
                } else if (card && !e.target.closest('button')) {
                    addToCart(parseInt(card.dataset.productId, 10));
                }
            });
        }

        // ── Delegación: controles de qty en carrito ──────────
        const cartItems = document.getElementById('posCartItems');
        if (cartItems) {
            cartItems.addEventListener('click', e => {
                const qtyBtn = e.target.closest('[data-qty-delta]');
                if (!qtyBtn) return;
                const productId = parseInt(qtyBtn.dataset.qtyProduct, 10);
                const delta     = parseInt(qtyBtn.dataset.qtyDelta, 10);
                updateQty(productId, delta);
            });
        }

        // ── Vaciar carrito ──────────────────────────────────
        const clearBtn = document.getElementById('posCartClear');
        if (clearBtn) clearBtn.addEventListener('click', clearCart);

        // ── Confirmar venta ─────────────────────────────────
        const confirmBtn = document.getElementById('posBtnConfirm');
        if (confirmBtn) confirmBtn.addEventListener('click', confirmSale);

        // ── Cart toggle / close ─────────────────────────────
        const toggleBtn = document.getElementById('posCartToggle');
        if (toggleBtn) toggleBtn.addEventListener('click', toggleCart);

        const closeBtn = document.getElementById('posCartClose');
        if (closeBtn) closeBtn.addEventListener('click', closeCart);
    });

})();
