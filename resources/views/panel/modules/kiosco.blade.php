{{-- MÓDULO: KIOSCO – Punto de Venta --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="pos-layout" id="posLayout">

    {{-- ════════════════════════════════════════════════════════
         CATÁLOGO (zona principal – ocupa todo si carrito colapsado)
    ════════════════════════════════════════════════════════════ --}}
    <div class="pos-catalog">

        {{-- Header del catálogo (fijo arriba) --}}
        <div class="pos-catalog-header">
            <h2 class="pos-catalog-title">
                <span class="pos-catalog-icon">🏪</span>
                Punto de Venta
            </h2>
            <div class="pos-search-wrap">
                <span class="pos-search-icon">🔍</span>
                <input type="text"
                       class="pos-search"
                       id="posSearch"
                       placeholder="Buscar producto..."
                       autocomplete="off">
            </div>
            {{-- Botón toggle del carrito --}}
            <button class="pos-cart-toggle" id="posCartToggle" title="Abrir carrito">
                <span class="pos-cart-toggle-icon">🛒</span>
                <span class="pos-cart-toggle-badge" id="posCartBadge" style="display:none">0</span>
            </button>
        </div>

        {{-- Chips de categorías (scroll horizontal) --}}
        <div class="pos-category-bar" id="posCategoryBar">
            <button class="pos-cat-chip active" data-cat-id="all">
                <span class="pos-cat-chip-icon">📋</span>
                <span>Todos</span>
            </button>
        </div>

        {{-- Grid de productos (scroll vertical independiente) --}}
        <div class="pos-product-grid" id="posProductGrid">
            <div class="pos-loading">
                <div class="pos-spinner"></div>
                <span>Cargando productos...</span>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════
         CARRITO (lateral derecho – colapsable)
    ════════════════════════════════════════════════════════════ --}}
    <div class="pos-cart" id="posCart">

        {{-- Header sticky --}}
        <div class="pos-cart-header">
            <h3 class="pos-cart-title">🛒 Carrito</h3>
            <div class="pos-cart-header-actions">
                <button class="pos-cart-clear" id="posCartClear" title="Vaciar carrito" style="display:none">
                    🗑️ Vaciar
                </button>
                <button class="pos-cart-close" id="posCartClose" title="Cerrar carrito">✕</button>
            </div>
        </div>

        {{-- Lista de items (scrolleable) --}}
        <div class="pos-cart-items" id="posCartItems">
            <div class="pos-cart-empty" id="posCartEmpty">
                <div class="pos-cart-empty-icon">🛒</div>
                <div class="pos-cart-empty-text">El carrito está vacío</div>
                <div class="pos-cart-empty-sub">Hacé click en un producto para agregarlo</div>
            </div>
        </div>

        {{-- Footer sticky (siempre visible cuando hay items) --}}
        <div class="pos-cart-footer" id="posCartFooter" style="display:none">
            <div class="pos-cart-summary">
                <div class="pos-cart-summary-row">
                    <span>Ítems</span>
                    <span id="posCartCount">0</span>
                </div>
                <div class="pos-cart-summary-row pos-cart-total-row">
                    <span>Total</span>
                    <span id="posCartTotal" class="pos-cart-total-value">$0</span>
                </div>
            </div>
            <button class="pos-btn-confirm" id="posBtnConfirm">
                <span class="pos-btn-confirm-icon">💳</span>
                Confirmar Venta
            </button>
        </div>

    </div>

</div>

{{-- Toast de notificaciones --}}
<div class="pos-toast-container" id="posToastContainer"></div>
