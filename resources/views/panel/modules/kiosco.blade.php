{{-- MÓDULO: KIOSCO – Punto de Venta --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="pos-layout" id="posLayout">

    {{-- ════════════════════════════════════════════════════════
         CATÁLOGO
    ════════════════════════════════════════════════════════ --}}
    <div class="pos-catalog">

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
            <button class="pos-cart-toggle" id="posCartToggle" title="Abrir carrito">
                <span class="pos-cart-toggle-icon">🛒</span>
                <span class="pos-cart-toggle-badge" id="posCartBadge" style="display:none">0</span>
            </button>
        </div>

        <div class="pos-category-bar" id="posCategoryBar">
            <button class="pos-cat-chip active" data-cat-id="all">
                <span class="pos-cat-chip-icon">📋</span>
                <span>Todos</span>
            </button>
        </div>

        <div class="pos-product-grid" id="posProductGrid">
            <div class="pos-loading">
                <div class="pos-spinner"></div>
                <span>Cargando productos...</span>
            </div>
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════════
         CARRITO
    ════════════════════════════════════════════════════════ --}}
    <div class="pos-cart" id="posCart">

        <div class="pos-cart-header">
            <h3 class="pos-cart-title">🛒 Carrito</h3>
            <div class="pos-cart-header-actions">
                <button class="pos-cart-clear" id="posCartClear" title="Vaciar carrito" style="display:none">
                    🗑️ Vaciar
                </button>
                <button class="pos-cart-close" id="posCartClose" title="Cerrar carrito">✕</button>
            </div>
        </div>

        <div class="pos-cart-items" id="posCartItems">
            <div class="pos-cart-empty" id="posCartEmpty">
                <div class="pos-cart-empty-icon">🛒</div>
                <div class="pos-cart-empty-text">El carrito está vacío</div>
                <div class="pos-cart-empty-sub">Hacé click en un producto para agregarlo</div>
            </div>
        </div>

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

{{-- ══════════════════════════════════════════════════════
     MODAL: CHECKOUT — Método de pago y estado
══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="posCheckoutModal">
    <div class="modal" style="max-width: min(550px, 90dvw);">
        <div class="modal-header">
            <h3 class="modal-title">💳 Confirmar Venta</h3>
            <button class="modal-close">✕</button>
        </div>
        <div class="modal-body" style="gap:18px">

            {{-- Resumen del carrito --}}
            <div class="pos-checkout-summary">
                <div class="pos-checkout-summary-row">
                    <span>Productos</span>
                    <span id="chkCount">—</span>
                </div>
                <div class="pos-checkout-summary-row pos-checkout-total-row">
                    <span>Total a cobrar</span>
                    <span id="chkTotal" class="pos-checkout-total-amount">$0</span>
                </div>
            </div>

            {{-- Campo cliente (opcional) --}}
            <div class="form-group">
                <label class="form-label">Cliente / Descripción <span style="color:var(--text-muted)">(opcional)</span></label>
                <input class="form-input" type="text" id="chkCliente" placeholder="Ej: Mesa 3, Juan García, cliente general…" maxlength="100">
            </div>

            {{-- Método de pago --}}
            <div class="form-group">
                <label class="form-label">Método de pago <span style="color:#2d8cff">*</span></label>
                <div class="pos-pay-methods">
                    <button type="button" class="pos-pay-method active" data-method="efectivo" id="chkMetodoEfectivo">
                        <span class="pos-pay-method-icon">💵</span>
                        <span>Efectivo</span>
                    </button>
                    <button type="button" class="pos-pay-method" data-method="transferencia" id="chkMetodoTransferencia">
                        <span class="pos-pay-method-icon">📲</span>
                        <span>Transferencia</span>
                    </button>
                </div>
            </div>

            {{-- Estado del pago --}}
            <div class="form-group">
                <label class="form-label">Estado del pago <span style="color:#2d8cff">*</span></label>
                <div class="pos-pay-states">
                    <button type="button" class="pos-pay-state active" data-state="pagado" id="chkEstadoPagado">
                        <span class="pos-pay-state-dot pos-pay-state-dot--green"></span>
                        <div>
                            <div class="pos-pay-state-label">Pagado</div>
                            <div class="pos-pay-state-sub">Se recibió el pago ahora</div>
                        </div>
                    </button>
                    <button type="button" class="pos-pay-state" data-state="pendiente" id="chkEstadoPendiente">
                        <span class="pos-pay-state-dot pos-pay-state-dot--yellow"></span>
                        <div>
                            <div class="pos-pay-state-label">Pendiente</div>
                            <div class="pos-pay-state-sub">Se cobrará más tarde</div>
                        </div>
                    </button>
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="form-group">
                <label class="form-label">Observaciones <span style="color:var(--text-muted)">(opcional)</span></label>
                <textarea class="form-input" id="chkObs" rows="2" placeholder="Notas adicionales…" style="resize:vertical;min-height:56px"></textarea>
            </div>

            <div class="modal-footer" style="padding-top:8px">
                <button type="button" class="btn-cancel" id="chkBtnCancel">Cancelar</button>
                <button type="button" class="btn-submit" id="chkBtnConfirm">
                    <span id="chkBtnIcon">💳</span> Registrar Venta
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Estilos específicos del checkout --}}
<style>
/* ── Resumen del checkout ── */
.pos-checkout-summary {
    background: rgba(45,140,255,.06);
    border: 1px solid rgba(45,140,255,.15);
    border-radius: 10px; padding: .85rem 1rem;
    display: flex; flex-direction: column; gap: .4rem;
}
.pos-checkout-summary-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: .85rem; color: var(--text-secondary);
}
.pos-checkout-total-row { margin-top: .2rem; padding-top: .4rem; border-top: 1px solid rgba(45,140,255,.14); }
.pos-checkout-total-amount { font-size: 1.25rem; font-weight: 800; color: var(--blue-light); }

/* ── Botones de método de pago ── */
.pos-pay-methods { display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; }
.pos-pay-method {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .75rem 1rem; border-radius: 10px; cursor: pointer; font-family: inherit;
    font-size: .88rem; font-weight: 600;
    background: rgba(255,255,255,.04); border: 2px solid rgba(255,255,255,.1);
    color: var(--text-secondary);
    transition: background .18s, border-color .18s, color .18s, box-shadow .18s;
}
.pos-pay-method-icon { font-size: 1.2rem; }
.pos-pay-method:hover { background: rgba(255,255,255,.08); color: var(--text-primary); border-color: rgba(255,255,255,.2); }
.pos-pay-method.active {
    background: rgba(45,140,255,.12); border-color: var(--blue-neon);
    color: var(--blue-light); box-shadow: 0 0 16px rgba(45,140,255,.2);
}

/* ── Botones de estado de pago ── */
.pos-pay-states { display: flex; flex-direction: column; gap: .5rem; }
.pos-pay-state {
    display: flex; align-items: center; gap: .75rem;
    padding: .7rem .9rem; border-radius: 10px; cursor: pointer; font-family: inherit;
    background: rgba(255,255,255,.04); border: 2px solid rgba(255,255,255,.1);
    text-align: left;
    transition: background .18s, border-color .18s, box-shadow .18s;
}
.pos-pay-state:hover { background: rgba(255,255,255,.07); border-color: rgba(255,255,255,.2); }
.pos-pay-state.active[data-state="pagado"]   { background: rgba(34,197,94,.08);  border-color: rgba(34,197,94,.4);  box-shadow: 0 0 14px rgba(34,197,94,.15); }
.pos-pay-state.active[data-state="pendiente"]{ background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.4); box-shadow: 0 0 14px rgba(251,191,36,.15); }
.pos-pay-state-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}
.pos-pay-state-dot--green  { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,.6); }
.pos-pay-state-dot--yellow { background: #f59e0b; box-shadow: 0 0 6px rgba(245,158,11,.6); }
.pos-pay-state-label { font-size: .88rem; font-weight: 600; color: var(--text-primary); }
.pos-pay-state-sub   { font-size: .74rem; color: var(--text-secondary); margin-top: .1rem; }
</style>
