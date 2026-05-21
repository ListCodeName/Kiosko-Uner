{{-- MÓDULO: PRODUCTOS --}}
@php
    use App\Models\Product;

    $total      = $products->count();
    $reventa    = $products->where('tipo', 'reventa');
    $insumos    = $products->where('tipo', 'insumo');
    $elaborados = $products->where('tipo', 'elaborado');
    $cntReventa    = $reventa->count();
    $cntInsumo     = $insumos->count();
    $cntElaborado  = $elaborados->count();
    $deletedCount  = isset($deletedProducts) ? $deletedProducts->count() : 0;
@endphp

<style>
/* ── STATS ── */
.prod-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:.75rem; margin-bottom:1.5rem; }
.prod-stat  { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1rem 1.2rem; }
.prod-stat .ps-label { font-size:.72rem; color:#8990a8; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
.prod-stat .ps-value { font-size:1.6rem; font-weight:800; margin-top:.2rem; }
.ps-purple{color:#8b85ff;} .ps-teal{color:#22d3a0;} .ps-amber{color:#fbbf24;} .ps-violet{color:#c084fc;} .ps-red{color:#ff6b6b;}

/* ── TIPO BADGES ── */
.ptipo { display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .6rem;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:.02em; }
.ptipo--reventa   { background:rgba(34,211,160,.1);  color:#22d3a0; border:1px solid rgba(34,211,160,.3); }
.ptipo--insumo    { background:rgba(251,191,36,.1);  color:#fbbf24; border:1px solid rgba(251,191,36,.3); }
.ptipo--elaborado { background:rgba(192,132,252,.1); color:#c084fc; border:1px solid rgba(192,132,252,.3); }

/* ── FILTER CHIPS ── */
.prod-filter-row { display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.2rem;align-items:center; }
.pchip { background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#8990a8;font-size:.75rem;padding:.3rem .75rem;border-radius:20px;cursor:pointer;transition:all .2s;font-weight:600;font-family:inherit; }
.pchip:hover { background:rgba(255,255,255,.12);color:#e8eaf0; }
.pchip.active { background:#6c63ff;color:#fff;border-color:#6c63ff; }
.pchip.active.reventa   { background:rgba(34,211,160,.2);color:#22d3a0;border-color:rgba(34,211,160,.5); }
.pchip.active.insumo    { background:rgba(251,191,36,.2);color:#fbbf24;border-color:rgba(251,191,36,.5); }
.pchip.active.elaborado { background:rgba(192,132,252,.2);color:#c084fc;border-color:rgba(192,132,252,.5); }

/* ── SEARCH ── */
.prod-search { position:relative; margin-bottom:1.25rem; }
.prod-search svg { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#8990a8; }
.prod-search input { width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.6rem .9rem .6rem 2.3rem;color:#e8eaf0;font-family:inherit;font-size:.9rem;outline:none;transition:border-color .2s; }
.prod-search input:focus { border-color:#6c63ff; }
.prod-search input::placeholder { color:#8990a8; }

/* ── TABLE ── */
.prod-table-wrap { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08); border-radius:12px; overflow:hidden; }
.prod-table-head { display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07);flex-wrap:wrap;gap:.5rem; }
.prod-table-head h3 { font-size:.95rem;font-weight:600;color:#e8eaf0; }
.prod-count { font-size:.78rem;color:#8990a8;background:rgba(255,255,255,.06);padding:.2rem .65rem;border-radius:20px;border:1px solid rgba(255,255,255,.08); }
table.ptable { width:100%;border-collapse:collapse; }
table.ptable thead th { background:rgba(255,255,255,.04);padding:.75rem 1rem;text-align:left;font-size:.72rem;font-weight:600;color:#8990a8;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap; }
table.ptable thead th:first-child { padding-left:1.5rem; }
table.ptable thead th:last-child  { padding-right:1.5rem;text-align:right; }
table.ptable tbody tr { border-bottom:1px solid rgba(255,255,255,.05);transition:background .15s; }
table.ptable tbody tr:last-child { border:none; }
table.ptable tbody tr:hover { background:rgba(108,99,255,.07); }
table.ptable tbody td { padding:.85rem 1rem;font-size:.88rem;vertical-align:middle; }
table.ptable tbody td:first-child { padding-left:1.5rem; }
table.ptable tbody td:last-child  { padding-right:1.5rem;text-align:right; }

/* ── CELLS ── */
.prank { display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;font-size:.75rem;font-weight:700;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#8990a8; }
.pname { font-weight:600;color:#e8eaf0; }
.pdesc { font-size:.75rem;color:#8990a8;margin-top:.15rem; }
.pprice { font-weight:700;color:#22d3a0;font-size:.88rem; }
.pprice--none { color:#4a4f66; font-size:.78rem; font-style: italic; }
.pstock { display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .65rem;border-radius:20px;font-size:.78rem;font-weight:600; }
.pstock.high   { background:rgba(34,211,160,.1);color:#22d3a0;border:1px solid rgba(34,211,160,.25); }
.pstock.medium { background:rgba(245,200,66,.1);color:#f5c842;border:1px solid rgba(245,200,66,.25); }
.pstock.low    { background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.25); }
.pstock.zero   { background:rgba(255,107,107,.15);color:#ff6b6b;border:1px solid #ff6b6b; }
.pstock.na     { background:rgba(255,255,255,.04);color:#8990a8;border:1px solid rgba(255,255,255,.1); }
.psdot { width:6px;height:6px;border-radius:50%;background:currentColor; }

/* ── DELETED ROWS ── */
.ptable tbody tr.prod-deleted { opacity:.35;pointer-events:none; }
.ptable tbody tr.prod-deleted .pname { text-decoration:line-through;color:#6b6f85; }
.pbadge-del { display:inline-block;font-size:.68rem;font-weight:700;padding:.1rem .5rem;border-radius:20px;background:rgba(255,107,107,.12);color:#ff6b6b;border:1px solid rgba(255,107,107,.25);letter-spacing:.04em;text-transform:uppercase;vertical-align:middle;margin-left:.4rem; }
.prod-sep td { padding:.5rem 1.5rem;background:rgba(255,255,255,.025);border-bottom:1px solid rgba(255,255,255,.07); }
.prod-sep-label { font-size:.7rem;font-weight:700;color:#6b6f85;text-transform:uppercase;letter-spacing:.08em; }

/* ── ACTION BTNS ── */
.pactions { display:flex;align-items:center;justify-content:flex-end;gap:.4rem; }
.pbtn { display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .72rem;border-radius:7px;font-family:inherit;font-size:.78rem;font-weight:500;cursor:pointer;border:none;transition:all .18s;white-space:nowrap; }
.pbtn-edit    { background:rgba(108,99,255,.15);color:#8b85ff;border:1px solid rgba(108,99,255,.3); }
.pbtn-edit:hover    { background:rgba(108,99,255,.3); }
.pbtn-load    { background:rgba(192,132,252,.12);color:#c084fc;border:1px solid rgba(192,132,252,.3); }
.pbtn-load:hover    { background:rgba(192,132,252,.28); }
.pbtn-baja    { background:rgba(251,191,36,.1);color:#fbbf24;border:1px solid rgba(251,191,36,.3); }
.pbtn-baja:hover    { background:rgba(251,191,36,.22); }
.pbtn-del     { background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.25); }
.pbtn-del:hover     { background:rgba(255,107,107,.25); }
.pbtn-new     { background:rgba(108,99,255,.2);color:#8b85ff;border:1px solid rgba(108,99,255,.4); }
.pbtn-new:hover     { background:rgba(108,99,255,.35); }
.pbtn-restore { background:rgba(245,200,66,.12);color:#f5c842;border:1px solid rgba(245,200,66,.3); }
.pbtn-restore:hover { background:rgba(245,200,66,.25); }

/* ── MODAL ── */
.pmodal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:500;align-items:center;justify-content:center;backdrop-filter:blur(5px); }
.pmodal-overlay.open { display:flex; }
.pmodal { background:#1a1d27;border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:1.75rem;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.6);animation:pmIn .22s ease; }
@keyframes pmIn { from{opacity:0;transform:scale(.94) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.pmodal-title { font-size:1.05rem;font-weight:700;margin-bottom:1.1rem;color:#e8eaf0; }
.pmodal-sub   { font-size:.87rem;color:#8990a8;margin-bottom:1.1rem;line-height:1.6; }
.pmodal-sub strong { color:#e8eaf0; }
.pmodal label { display:block;font-size:.78rem;font-weight:600;color:#8990a8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem; }
.pmodal input[type=text],.pmodal input[type=number],.pmodal textarea { width:100%;background:#0f1117;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.65rem .9rem;color:#e8eaf0;font-family:inherit;font-size:.92rem;outline:none;margin-bottom:1rem;transition:border-color .2s; }
.pmodal input:focus,.pmodal textarea:focus { border-color:#6c63ff; }
.pmodal textarea { resize:vertical;min-height:80px; }
.pmodal select { width:100%;background:#0f1117;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.65rem .9rem;color:#e8eaf0;font-family:inherit;font-size:.92rem;outline:none;margin-bottom:.5rem;transition:border-color .2s;appearance:none;cursor:pointer; }
.pmodal select:focus { border-color:#6c63ff; }

/* Descripción del tipo seleccionado */
.ptipo-hint { font-size:.78rem;border-radius:8px;padding:.5rem .8rem;margin-bottom:1rem;transition:all .2s; }
.ptipo-hint--reventa   { background:rgba(34,211,160,.08); color:#22d3a0; border:1px solid rgba(34,211,160,.2); }
.ptipo-hint--insumo    { background:rgba(251,191,36,.08); color:#fbbf24; border:1px solid rgba(251,191,36,.2); }
.ptipo-hint--elaborado { background:rgba(192,132,252,.08);color:#c084fc; border:1px solid rgba(192,132,252,.2); }

.pmodal-row { display:grid;grid-template-columns:1fr 1fr;gap:.85rem; }
.pmodal-footer { display:flex;gap:.65rem;justify-content:flex-end;margin-top:.5rem; }
.pbtn-cancel  { background:rgba(255,255,255,.06);color:#8990a8;border:1px solid rgba(255,255,255,.1); }
.pbtn-confirm { background:linear-gradient(135deg,#6c63ff,#8b85ff);color:#fff;border:none;box-shadow:0 4px 14px rgba(108,99,255,.35); }
.pbtn-confirm:hover { opacity:.88; }
.pbtn-danger  { background:linear-gradient(135deg,#ff4757,#ff6b6b);color:#fff;border:none; }
.pbtn-danger:hover { opacity:.85; }

/* ── PRECIO INFO ── */
.pprice-hint { font-size:.72rem;color:#8990a8;margin-top:-.5rem;margin-bottom:.75rem;font-style:italic; }
</style>

{{-- STAT CARDS --}}
<div class="prod-stats">
    <div class="prod-stat">
        <div class="ps-label">Total</div>
        <div class="ps-value ps-purple">{{ $total }}</div>
    </div>
    <div class="prod-stat">
        <div class="ps-label">🛍️ Reventa</div>
        <div class="ps-value ps-teal">{{ $cntReventa }}</div>
        <div style="font-size:.72rem;color:#8990a8;margin-top:.3rem">Stock controlado por compra</div>
    </div>
    <div class="prod-stat">
        <div class="ps-label">🧂 Insumos</div>
        <div class="ps-value ps-amber">{{ $cntInsumo }}</div>
        <div style="font-size:.72rem;color:#8990a8;margin-top:.3rem">Sin control de stock</div>
    </div>
    <div class="prod-stat">
        <div class="ps-label">🍕 Elaborados</div>
        <div class="ps-value ps-violet">{{ $cntElaborado }}</div>
        <div style="font-size:.72rem;color:#8990a8;margin-top:.3rem">Carga manual de unidades</div>
    </div>
    <div class="prod-stat">
        <div class="ps-label">Fecha y Hora</div>
        <div class="ps-value ps-purple" style="font-size:.88rem;line-height:1.2;margin-top:.4rem">
            <div>{{ now()->translatedFormat('d/m/Y') }}</div>
            <div style="font-size:1.15rem;font-weight:800;margin-top:.1rem">{{ now()->translatedFormat('h:i A') }}</div>
        </div>
    </div>
</div>

{{-- FILTER + SEARCH --}}
<div class="prod-filter-row">
    <span style="font-size:.78rem;color:#8990a8;font-weight:600">Filtrar:</span>
    <button class="pchip active" data-tipo-filter="all" onclick="prodFilterTipo('all',this)">📋 Todos</button>
    <button class="pchip reventa" data-tipo-filter="reventa" onclick="prodFilterTipo('reventa',this)">🛍️ Reventa</button>
    <button class="pchip insumo" data-tipo-filter="insumo" onclick="prodFilterTipo('insumo',this)">🧂 Insumos</button>
    <button class="pchip elaborado" data-tipo-filter="elaborado" onclick="prodFilterTipo('elaborado',this)">🍕 Elaborados</button>
</div>

<div class="prod-search">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input id="prod-search-input" type="text" placeholder="Buscar producto..." autocomplete="off">
</div>

{{-- TABLE --}}
<div class="prod-table-wrap">
    <div class="prod-table-head">
        <h3>📦 Catálogo de Productos</h3>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
            <span class="prod-count" id="prodCountLabel">{{ $total }} {{ $total === 1 ? 'producto' : 'productos' }}</span>
            <button class="pbtn pbtn-new" onclick="openProdNew()">&#43; Nuevo Producto</button>
            @if($deletedCount > 0)
            <button class="pbtn pbtn-restore" onclick="openProdRestore()">&#9851; Reactivar ({{ $deletedCount }})</button>
            @endif
        </div>
    </div>

    @if($products->isEmpty())
        <div style="padding:3.5rem;text-align:center;color:#8990a8">
            <div style="font-size:3rem;opacity:.4;margin-bottom:.75rem">📭</div>
            <p>No hay productos activos. Creá el primero.</p>
        </div>
    @else
    <table class="ptable" id="prod-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Precio / Stock</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            @php
                $tipo = $product->tipo;
                // Stock display
                if ($tipo === 'insumo') {
                    $stockHtml = '<span class="pstock na"><span class="psdot"></span>N/A (insumo)</span>';
                } else {
                    $sc = $product->stock == 0 ? 'zero' : ($product->stock < 5 ? 'medium' : 'high');
                    $sl = match($sc){'zero'=>'Sin stock','medium'=>$product->stock.' (bajo)','high'=>$product->stock.' uds.'};
                    $stockHtml = '<span class="pstock '.$sc.'"><span class="psdot"></span>'.$sl.'</span>';
                }
                // Precio display
                if ($product->price > 0) {
                    $precioHtml = '<span class="pprice">$'.number_format($product->price,2,',','.').'</span>';
                } else {
                    $precioHtml = '<span class="pprice--none">Sin precio aún</span>';
                }
            @endphp
            <tr data-tipo="{{ $tipo }}" data-pname="{{ strtolower($product->name) }}">
                <td><span class="prank">{{ $i+1 }}</span></td>
                <td>
                    <div class="pname">{{ $product->name }}</div>
                    @if($product->description)<div class="pdesc">{{ Str::limit($product->description,55) }}</div>@endif
                </td>
                <td>
                    <span class="ptipo ptipo--{{ $tipo }}">
                        @if($tipo==='reventa') 🛍️ Reventa
                        @elseif($tipo==='insumo') 🧂 Insumo
                        @else 🍕 Elaborado
                        @endif
                    </span>
                </td>
                <td>
                    <div style="display:flex;flex-direction:column;gap:.25rem">
                        {!! $precioHtml !!}
                        {!! $stockHtml !!}
                    </div>
                </td>
                <td>
                    <div class="pactions">
                        {{-- Editar (solo nombre, desc, tipo) --}}
                        <button class="pbtn pbtn-edit"
                            onclick="openProdEdit({{ $product->id }},'{{ addslashes($product->name) }}','{{ addslashes($product->description ?? '') }}','{{ $product->tipo }}')">
                            ✏️ Editar
                        </button>

                        @if($tipo === 'elaborado')
                        {{-- Cargar unidades producidas --}}
                        <button class="pbtn pbtn-load"
                            onclick="openCargarUnidades({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->stock }},{{ $product->price }})">
                            📦 Cargar
                        </button>
                        {{-- Dar de baja sobrantes --}}
                        @if($product->stock > 0)
                        <button class="pbtn pbtn-baja"
                            onclick="openBajaSobrantes({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->stock }})">
                            ↩️ Baja
                        </button>
                        @endif
                        @endif

                        {{-- Eliminar (soft delete) --}}
                        <button class="pbtn pbtn-del"
                            onclick="openProdDelete({{ $product->id }},'{{ addslashes($product->name) }}')">
                            🗑️
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach

            {{-- Eliminados --}}
            @if(isset($deletedProducts) && $deletedProducts->count() > 0)
            <tr class="prod-sep" id="prod-deleted-sep">
                <td colspan="5">
                    <span class="prod-sep-label">🗑️ Desactivados ({{ $deletedProducts->count() }})</span>
                </td>
            </tr>
            @foreach($deletedProducts as $product)
            <tr class="prod-deleted">
                <td><span class="prank">–</span></td>
                <td>
                    <div class="pname">{{ $product->name }} <span class="pbadge-del">Inactivo</span></div>
                    @if($product->description)<div class="pdesc">{{ Str::limit($product->description,55) }}</div>@endif
                </td>
                <td><span class="ptipo ptipo--{{ $product->tipo }}">{{ ucfirst($product->tipo) }}</span></td>
                <td>—</td>
                <td>
                    <div class="pactions">
                        <button class="pbtn pbtn-restore" style="pointer-events:all"
                            onclick="openProdReactivar({{ $product->id }},'{{ addslashes($product->name) }}')">
                            &#9851; Reactivar
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    @endif
</div>

{{-- ══════════════════ MODAL: NUEVO PRODUCTO ══════════════════ --}}
<div class="pmodal-overlay" id="prodNewModal">
    <div class="pmodal">
        <div class="pmodal-title">&#43; Nuevo Producto</div>
        <form id="prodNewForm" method="POST" action="/products">
            @csrf
            <label for="pn-name">Nombre del producto</label>
            <input type="text" id="pn-name" name="name" required maxlength="255" placeholder="Ej: Coca-Cola 500ml, Harina 000, Pizza mozzarella…">

            <label for="pn-tipo">Tipo de producto</label>
            <select id="pn-tipo" name="tipo" required onchange="updateTipoHint('pn-hint',this.value)">
                <option value="reventa">🛍️ Reventa — para vender tal cual se compra</option>
                <option value="insumo">🧂 Insumo — materia prima para elaborar</option>
                <option value="elaborado">🍕 Elaborado — producción propia para vender</option>
            </select>
            <div class="ptipo-hint ptipo-hint--reventa" id="pn-hint">
                🛍️ <strong>Reventa:</strong> El precio y stock se actualizan automáticamente cuando registrás una compra.
            </div>

            <label for="pn-desc">Descripción (opcional)</label>
            <textarea id="pn-desc" name="description" maxlength="1000" placeholder="Descripción, presentación, variedad…"></textarea>

            <div style="background:rgba(108,99,255,.08);border:1px solid rgba(108,99,255,.18);border-radius:8px;padding:.55rem .8rem;font-size:.78rem;color:#8b85ff;margin-bottom:1rem">
                ℹ️ El precio y stock <strong>no se cargan aquí</strong>. Se actualizan automáticamente al registrar compras o cargar unidades.
            </div>

            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdNew()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">✅ Crear Producto</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: EDITAR PRODUCTO ══════════════════ --}}
<div class="pmodal-overlay" id="prodEditModal">
    <div class="pmodal">
        <div class="pmodal-title">✏️ Editar Producto</div>
        <form id="prodEditForm" method="POST">
            @csrf @method('PUT')
            <label for="pe-name">Nombre</label>
            <input type="text" id="pe-name" name="name" required maxlength="255">

            <label for="pe-tipo">Tipo</label>
            <select id="pe-tipo" name="tipo" required onchange="updateTipoHint('pe-hint',this.value)">
                <option value="reventa">🛍️ Reventa</option>
                <option value="insumo">🧂 Insumo</option>
                <option value="elaborado">🍕 Elaborado</option>
            </select>
            <div class="ptipo-hint ptipo-hint--reventa" id="pe-hint" style="margin-bottom:.75rem"></div>

            <label for="pe-desc">Descripción (opcional)</label>
            <textarea id="pe-desc" name="description" maxlength="1000"></textarea>

            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdEdit()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: ELIMINAR ══════════════════ --}}
<div class="pmodal-overlay" id="prodDelModal">
    <div class="pmodal">
        <div class="pmodal-title">🗑️ Desactivar Producto</div>
        <p class="pmodal-sub" id="pdel-label"></p>
        <form id="prodDelForm" method="POST">
            @csrf @method('DELETE')
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdDel()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-danger">Sí, desactivar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: REACTIVAR INDIVIDUAL ══════════════════ --}}
<div class="pmodal-overlay" id="prodReactivarModal">
    <div class="pmodal">
        <div class="pmodal-title">&#9851; Reactivar Producto</div>
        <p class="pmodal-sub" id="preact-label"></p>
        <form id="prodReactivarForm" method="POST">
            @csrf @method('PATCH')
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="window.closeModal('prodReactivarModal')">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">&#9851; Reactivar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: REPONER ELIMINADOS (batch) ══════════════════ --}}
<div class="pmodal-overlay" id="prodRestoreModal">
    <div class="pmodal">
        <div class="pmodal-title">&#9851; Reactivar Producto Desactivado</div>
        <p class="pmodal-sub">Seleccioná el producto a reactivar. El precio y stock se actualizarán con la próxima compra.</p>
        <form id="prodRestoreForm" method="POST">
            @csrf @method('PATCH')
            <label for="pr-select">Producto desactivado</label>
            <select id="pr-select" required onchange="updateRestoreAction(this.value)">
                <option value="">— Seleccionar —</option>
                @if(isset($deletedProducts))
                @foreach($deletedProducts as $dp)
                <option value="{{ $dp->id }}">{{ $dp->name }} ({{ ucfirst($dp->tipo) }})</option>
                @endforeach
                @endif
            </select>
            <div class="pmodal-footer" style="margin-top:.5rem">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdRestore()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm" id="pr-submit" disabled>&#9851; Reactivar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: CARGAR UNIDADES (Elaborados) ══════════════════ --}}
<div class="pmodal-overlay" id="prodCargarModal">
    <div class="pmodal">
        <div class="pmodal-title">📦 Cargar Unidades Producidas</div>
        <p class="pmodal-sub" id="pcargar-label"></p>
        <form id="prodCargarForm" method="POST">
            @csrf
            <label for="pc-unidades">Unidades producidas a cargar</label>
            <input type="number" id="pc-unidades" name="unidades" min="1" required placeholder="Ej: 5 bandejas, 10 pizetas…">

            <label for="pc-precio">Precio de venta (opcional)</label>
            <div style="position:relative">
                <span style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#8990a8;font-weight:600">$</span>
                <input type="number" id="pc-precio" name="precio" step="0.01" min="0" placeholder="0.00" style="padding-left:1.8rem">
            </div>
            <p class="pprice-hint">Dejá en blanco para mantener el precio actual</p>

            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeCargarUnidades()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm" style="background:linear-gradient(135deg,#7c3aed,#c084fc)">📦 Confirmar Carga</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: BAJA SOBRANTES (Elaborados) ══════════════════ --}}
<div class="pmodal-overlay" id="prodBajaModal">
    <div class="pmodal">
        <div class="pmodal-title">↩️ Dar de Baja Sobrantes</div>
        <p class="pmodal-sub" id="pbaja-label"></p>
        <form id="prodBajaForm" method="POST">
            @csrf
            <label for="pb-sobrantes">Unidades sobrantes a dar de baja</label>
            <input type="number" id="pb-sobrantes" name="sobrantes" min="1" required placeholder="Cantidad sobrante">
            <p class="pprice-hint" id="pb-stock-hint"></p>

            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeBajaSobrantes()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-danger">↩️ Dar de Baja</button>
            </div>
        </form>
    </div>
</div>

<script>
/* ── Tipo hints ── */
const tipoHints = {
    reventa:   '🛍️ <strong>Reventa:</strong> El precio y stock se actualizan al registrar una compra.',
    insumo:    '🧂 <strong>Insumo:</strong> Materia prima para elaborar. No se controla stock.',
    elaborado: '🍕 <strong>Elaborado:</strong> Cargás las unidades producidas manualmente y podés dar de baja los sobrantes.',
};
function updateTipoHint(elId, tipo) {
    const el = document.getElementById(elId);
    if (!el) return;
    el.innerHTML = tipoHints[tipo] || '';
    el.className = 'ptipo-hint ptipo-hint--' + tipo;
}

/* ── Filtro por tipo ── */
let activeTipoFilter = 'all';
function prodFilterTipo(tipo, btn) {
    activeTipoFilter = tipo;
    document.querySelectorAll('.pchip').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    applyFilters();
}

/* ── Live search ── */
document.getElementById('prod-search-input').addEventListener('input', applyFilters);

function applyFilters() {
    const q    = document.getElementById('prod-search-input').value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('#prod-table tbody tr').forEach(r => {
        if (r.classList.contains('prod-sep') || r.classList.contains('prod-deleted')) {
            r.style.display = '';
            return;
        }
        const name  = r.dataset.pname || '';
        const tipo  = r.dataset.tipo  || '';
        const matchSearch = name.includes(q);
        const matchTipo   = activeTipoFilter === 'all' || tipo === activeTipoFilter;
        const show = matchSearch && matchTipo;
        r.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const lbl = document.getElementById('prodCountLabel');
    if (lbl) lbl.textContent = visible + ' producto' + (visible !== 1 ? 's' : '');
}

/* ── Nuevo Producto ── */
function openProdNew() {
    document.getElementById('prodNewForm').reset();
    updateTipoHint('pn-hint', 'reventa');
    window.openModal('prodNewModal');
    document.getElementById('pn-name').focus();
}
function closeProdNew() { window.closeModal('prodNewModal'); }

/* ── Editar Producto ── */
function openProdEdit(id, name, desc, tipo) {
    document.getElementById('pe-name').value = name;
    document.getElementById('pe-desc').value = desc;
    document.getElementById('pe-tipo').value = tipo;
    updateTipoHint('pe-hint', tipo);
    document.getElementById('prodEditForm').action = '/products/' + id;
    window.openModal('prodEditModal');
}
function closeProdEdit() { window.closeModal('prodEditModal'); }

/* ── Eliminar ── */
function openProdDelete(id, name) {
    document.getElementById('pdel-label').innerHTML =
        'El producto <strong>' + name + '</strong> será desactivado del sistema. Podrás reactivarlo luego.';
    document.getElementById('prodDelForm').action = '/products/' + id;
    window.openModal('prodDelModal');
}
function closeProdDel() { window.closeModal('prodDelModal'); }

/* ── Reactivar individual ── */
function openProdReactivar(id, name) {
    document.getElementById('preact-label').innerHTML =
        '¿Reactivar el producto <strong>' + name + '</strong>? Quedará disponible nuevamente en el catálogo.';
    document.getElementById('prodReactivarForm').action = '/products/' + id + '/restore';
    window.openModal('prodReactivarModal');
}

/* ── Reponer batch ── */
function openProdRestore() {
    document.getElementById('prodRestoreForm').reset();
    document.getElementById('pr-submit').disabled = true;
    window.openModal('prodRestoreModal');
}
function closeProdRestore() { window.closeModal('prodRestoreModal'); }
function updateRestoreAction(id) {
    if (id) {
        document.getElementById('prodRestoreForm').action = '/products/' + id + '/restore';
        document.getElementById('pr-submit').disabled = false;
    } else {
        document.getElementById('pr-submit').disabled = true;
    }
}

/* ── Cargar Unidades (Elaborados) ── */
function openCargarUnidades(id, name, stock, precio) {
    document.getElementById('pcargar-label').innerHTML =
        'Producto: <strong>' + name + '</strong><br>' +
        '<span style="font-size:.8rem;color:#8990a8">Stock actual: ' + stock + ' unidades' +
        (precio > 0 ? ' · Precio: $' + precio.toLocaleString('es-AR') : '') + '</span>';
    document.getElementById('pc-unidades').value = '';
    document.getElementById('pc-precio').value   = precio > 0 ? precio : '';
    document.getElementById('prodCargarForm').action = '/products/' + id + '/cargar-unidades';
    window.openModal('prodCargarModal');
    document.getElementById('pc-unidades').focus();
}
function closeCargarUnidades() { window.closeModal('prodCargarModal'); }

/* ── Baja Sobrantes (Elaborados) ── */
function openBajaSobrantes(id, name, stock) {
    document.getElementById('pbaja-label').innerHTML =
        'Producto: <strong>' + name + '</strong>';
    document.getElementById('pb-stock-hint').textContent =
        'Stock actual: ' + stock + ' unidades. Ingresá cuántas no se vendieron.';
    document.getElementById('pb-sobrantes').value = '';
    document.getElementById('pb-sobrantes').max   = stock;
    document.getElementById('prodBajaForm').action = '/products/' + id + '/baja-sobrantes';
    window.openModal('prodBajaModal');
    document.getElementById('pb-sobrantes').focus();
}
function closeBajaSobrantes() { window.closeModal('prodBajaModal'); }

/* ── Toast session ── */
@if(session('success'))
(function(){
    const t = document.createElement('div');
    t.textContent = @json(session('success'));
    t.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:rgba(34,211,160,.15);color:#22d3a0;border:1px solid rgba(34,211,160,.4);padding:.85rem 1.4rem;border-radius:10px;font-size:.9rem;font-weight:500;z-index:999;max-width:340px;';
    document.body.appendChild(t);
    setTimeout(()=>{ t.style.opacity='0'; setTimeout(()=>t.remove(),400); }, 4000);
})();
@endif
@if(session('error'))
(function(){
    const t = document.createElement('div');
    t.textContent = @json(session('error'));
    t.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:rgba(255,107,107,.15);color:#ff6b6b;border:1px solid rgba(255,107,107,.4);padding:.85rem 1.4rem;border-radius:10px;font-size:.9rem;font-weight:500;z-index:999;';
    document.body.appendChild(t);
    setTimeout(()=>t.remove(), 4500);
})();
@endif
</script>
