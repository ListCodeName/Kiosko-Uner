{{-- MÓDULO: PRODUCTOS (Sergio - aguilarsergio2302-ui) --}}
@php
    $total          = $products->count();
    $sinStock       = $products->where('stock', 0)->count();
    $stockBajo      = $products->where('stock', '>', 0)->where('stock', '<', 5)->count();
    $stockNormal    = $products->where('stock', '>=', 5)->count();
    $deletedCount   = isset($deletedProducts) ? $deletedProducts->count() : 0;
@endphp

<style>
/* ── STATS ── */
.prod-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:.75rem; margin-bottom:1.5rem; }
.prod-stat  { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:1rem 1.2rem; }
.prod-stat .ps-label { font-size:.72rem; color:#8990a8; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
.prod-stat .ps-value { font-size:1.6rem; font-weight:800; margin-top:.2rem; }
.ps-purple{color:#8b85ff;} .ps-green{color:#22d3a0;} .ps-yellow{color:#f5c842;} .ps-red{color:#ff6b6b;}
.ps-filter-btn { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:#8990a8; font-size:.62rem; padding:.25rem .5rem; border-radius:6px; cursor:pointer; margin-top:.6rem; display:inline-flex; align-items:center; gap:.3rem; transition:all .2s; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
.ps-filter-btn:hover { background:rgba(255,255,255,.12); color:#e8eaf0; border-color:rgba(255,255,255,.2); }
.ps-filter-btn.active { background:#6c63ff; color:#fff; border-color:#6c63ff; }

/* ── SEARCH ── */
.prod-search { position:relative; margin-bottom:1.25rem; }
.prod-search svg { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#8990a8; }
.prod-search input { width:100%;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.6rem .9rem .6rem 2.3rem;color:#e8eaf0;font-family:inherit;font-size:.9rem;outline:none;transition:border-color .2s; }
.prod-search input:focus { border-color:#6c63ff; }
.prod-search input::placeholder { color:#8990a8; }

/* ── TABLE ── */
.prod-table-wrap { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08); border-radius:12px; overflow:hidden; }
.prod-table-head { display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.07); }
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
.prank.gold   { background:rgba(245,200,66,.15);border-color:#f5c842;color:#f5c842; }
.prank.silver { background:rgba(180,180,190,.12);border-color:#aaa;color:#ccc; }
.prank.bronze { background:rgba(180,100,60,.12);border-color:#c87941;color:#c87941; }
.pname { font-weight:600;color:#e8eaf0; }
.pdesc { font-size:.75rem;color:#8990a8;margin-top:.15rem; }
.pprice { font-weight:700;color:#22d3a0;font-size:.95rem; }
.pstock { display:inline-flex;align-items:center;gap:.3rem;padding:.18rem .65rem;border-radius:20px;font-size:.78rem;font-weight:600; }
.pstock.high   { background:rgba(34,211,160,.1);color:#22d3a0;border:1px solid rgba(34,211,160,.25); }
.pstock.medium { background:rgba(245,200,66,.1);color:#f5c842;border:1px solid rgba(245,200,66,.25); }
.pstock.low    { background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.25); }
.pstock.zero   { background:rgba(255,107,107,.15);color:#ff6b6b;border:1px solid #ff6b6b; }
.psdot { width:6px;height:6px;border-radius:50%;background:currentColor; }

/* ── DELETED ROWS ── */
.ptable tbody tr.prod-deleted { opacity:.38; pointer-events:none; }
.ptable tbody tr.prod-deleted:hover { background:transparent; }
.ptable tbody tr.prod-deleted .pname { text-decoration:line-through; color:#6b6f85; }
.ptable tbody tr.prod-deleted .pdesc { color:#4a4f66; }
.ptable tbody tr.prod-deleted .pprice { color:#4a4f66; text-decoration:line-through; }
.pbadge-del { display:inline-block;font-size:.68rem;font-weight:700;padding:.1rem .5rem;border-radius:20px;background:rgba(255,107,107,.12);color:#ff6b6b;border:1px solid rgba(255,107,107,.25);letter-spacing:.04em;text-transform:uppercase;vertical-align:middle;margin-left:.4rem; }
.prod-sep td { padding:.5rem 1.5rem;background:rgba(255,255,255,.025);border-bottom:1px solid rgba(255,255,255,.07); }
.prod-sep-label { font-size:.7rem;font-weight:700;color:#6b6f85;text-transform:uppercase;letter-spacing:.08em; }

/* ── ACTION BTNS ── */
.pactions { display:flex;align-items:center;justify-content:flex-end;gap:.4rem; }
.pbtn { display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .72rem;border-radius:7px;font-family:inherit;font-size:.78rem;font-weight:500;cursor:pointer;border:none;transition:all .18s;white-space:nowrap;text-decoration:none; }
.pbtn-edit   { background:rgba(108,99,255,.15);color:#8b85ff;border:1px solid rgba(108,99,255,.3); }
.pbtn-edit:hover   { background:rgba(108,99,255,.3); }
.pbtn-stock  { background:rgba(34,211,160,.1);color:#22d3a0;border:1px solid rgba(34,211,160,.25); }
.pbtn-stock:hover  { background:rgba(34,211,160,.25); }
.pbtn-del    { background:rgba(255,107,107,.1);color:#ff6b6b;border:1px solid rgba(255,107,107,.25); }
.pbtn-del:hover    { background:rgba(255,107,107,.25); }
.pbtn-new     { background:rgba(108,99,255,.2);color:#8b85ff;border:1px solid rgba(108,99,255,.4); }
.pbtn-new:hover     { background:rgba(108,99,255,.35); }
.pbtn-restore { background:rgba(245,200,66,.12);color:#f5c842;border:1px solid rgba(245,200,66,.3); }
.pbtn-restore:hover { background:rgba(245,200,66,.25); }

/* ── SELECT dentro de modal ── */
.pmodal select { width:100%;background:#0f1117;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.65rem .9rem;color:#e8eaf0;font-family:inherit;font-size:.92rem;outline:none;margin-bottom:1rem;transition:border-color .2s;appearance:none;cursor:pointer; }
.pmodal select:focus { border-color:#6c63ff; }
.pmodal select option { background:#1a1d27; }

/* ── EMPTY ── */
.prod-empty { padding:3.5rem;text-align:center;color:#8990a8; }
.prod-empty .pe-icon { font-size:3rem;opacity:.4;margin-bottom:.75rem; }

/* ── MODAL ── */
.pmodal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:500;align-items:center;justify-content:center;backdrop-filter:blur(5px); }
.pmodal-overlay.open { display:flex; }
.pmodal { background:#1a1d27;border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:1.75rem;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.6);animation:pmIn .22s ease; }
@keyframes pmIn { from{opacity:0;transform:scale(.94) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.pmodal-title { font-size:1.05rem;font-weight:700;margin-bottom:1.1rem;color:#e8eaf0; }
.pmodal-sub   { font-size:.87rem;color:#8990a8;margin-bottom:1.1rem;line-height:1.6; }
.pmodal-sub strong { color:#e8eaf0; }
.pmodal label { display:block;font-size:.78rem;font-weight:600;color:#8990a8;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem; }
.pmodal input[type=text],.pmodal input[type=number],.pmodal textarea { width:100%;background:#0f1117;border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:.65rem .9rem;color:#e8eaf0;font-family:inherit;font-size:.92rem;outline:none;margin-bottom:1rem;transition:border-color .2s; }
.pmodal input:focus,.pmodal textarea:focus { border-color:#6c63ff; }
.pmodal textarea { resize:vertical;min-height:80px; }
.pmodal-prefix-wrap { position:relative; }
.pmodal-prefix { position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#8990a8;font-weight:600;pointer-events:none; }
.pmodal-prefix-wrap input { padding-left:1.8rem; }
.pmodal-row { display:grid;grid-template-columns:1fr 1fr;gap:.85rem; }
.pmodal-footer { display:flex;gap:.65rem;justify-content:flex-end;margin-top:.5rem; }
.pbtn-cancel  { background:rgba(255,255,255,.06);color:#8990a8;border:1px solid rgba(255,255,255,.1); }
.pbtn-confirm { background:linear-gradient(135deg,#6c63ff,#8b85ff);color:#fff;border:none;box-shadow:0 4px 14px rgba(108,99,255,.35); }
.pbtn-confirm:hover { opacity:.88; }
.pbtn-danger  { background:linear-gradient(135deg,#ff4757,#ff6b6b);color:#fff;border:none; }
.pbtn-danger:hover { opacity:.85; }
.perr { color:#ff6b6b;font-size:.78rem;margin-top:-.6rem;margin-bottom:.75rem; }
</style>

{{-- STAT CARDS --}}
<div class="prod-stats">
    <div class="prod-stat">
        <div class="ps-label">Total</div>
        <div class="ps-value ps-purple">{{ $total }}</div>
        <button class="ps-filter-btn active" onclick="filterByStock('all', this)">👁️ Ver Todos</button>
    </div>
    <div class="prod-stat">
        <div class="ps-label">Stock Normal</div>
        <div class="ps-value ps-green">{{ $stockNormal }}</div>
        <button class="ps-filter-btn" onclick="filterByStock('high', this)">🔍 Buscar Normales</button>
    </div>
    <div class="prod-stat">
        <div class="ps-label">Stock Bajo</div>
        <div class="ps-value ps-yellow">{{ $stockBajo }}</div>
        <button class="ps-filter-btn" onclick="filterByStock('medium', this)">🔍 Buscar Bajos</button>
    </div>
    <div class="prod-stat">
        <div class="ps-label">Sin Stock</div>
        <div class="ps-value ps-red">{{ $sinStock }}</div>
        <button class="ps-filter-btn" onclick="filterByStock('zero', this)">🎯 Seleccionar Sin Stock</button>
    </div>
    <div class="prod-stat">
        <div class="ps-label">Fecha y Hora</div>
        <div class="ps-value ps-purple" style="font-size:0.88rem; line-height:1.2; margin-top:.4rem;">
            <div>{{ now()->translatedFormat('d/m/Y') }}</div>
            <div style="font-size:1.15rem; font-weight:800; margin-top:.1rem;">{{ now()->translatedFormat('h:i A') }}</div>
        </div>
    </div>
</div>

{{-- SEARCH --}}
<div class="prod-search">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input id="prod-search-input" type="text" placeholder="Buscar producto..." autocomplete="off">
</div>

{{-- TABLE --}}
<div class="prod-table-wrap">
    <div class="prod-table-head">
        <h3>📦 Productos — Ordenados alfabéticamente A-Z</h3>
        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
            <span class="prod-count">{{ $total }} {{ $total === 1 ? 'producto' : 'productos' }}</span>
            <button class="pbtn pbtn-new" onclick="openProdNew()">&#43; Nuevo Producto</button>
            @if($deletedCount > 0)
            <button class="pbtn pbtn-restore" onclick="openProdRestore()">&#9851; Reponer Eliminados ({{ $deletedCount }})</button>
            @endif
        </div>
    </div>

    @if($products->isEmpty())
        <div class="prod-empty">
            <div class="pe-icon">📭</div>
            <p>No hay productos activos en el sistema.</p>
        </div>
    @else
    <table class="ptable" id="prod-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Precio ↓</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            @php
                $rc = $i===0?'gold':($i===1?'silver':($i===2?'bronze':''));
                $sc = $product->stock == 0 ? 'zero' : ($product->stock < 5 ? 'medium' : 'high');
                $sl = match($sc){'zero'=>'Sin stock','medium'=>$product->stock.' (Bajo)','high'=>$product->stock.' (OK)'};
            @endphp
            <tr data-stock-status="{{ $sc }}">
                <td><span class="prank {{ $rc }}">{{ $i+1 }}</span></td>
                <td>
                    <div class="pname">{{ $product->name }}</div>
                    @if($product->description)<div class="pdesc">{{ Str::limit($product->description,50) }}</div>@endif
                </td>
                <td><span class="pprice">${{ number_format($product->price,2,',','.') }}</span></td>
                <td><span class="pstock {{ $sc }}"><span class="psdot"></span>{{ $sl }}</span></td>
                <td>
                    <div class="pactions">
                        <button class="pbtn pbtn-edit"
                            onclick="openProdEdit({{ $product->id }},'{{ addslashes($product->name) }}','{{ addslashes($product->description ?? '') }}',{{ $product->price }},{{ $product->stock }})">
                            ✏️ Editar
                        </button>
                        <button class="pbtn pbtn-stock"
                            onclick="openProdStock({{ $product->id }},'{{ addslashes($product->name) }}',{{ $product->stock }})">
                            📦 Stock
                        </button>
                        <button class="pbtn pbtn-del"
                            onclick="openProdDelete({{ $product->id }},'{{ addslashes($product->name) }}')">
                            🗑️
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach

            {{-- ── PRODUCTOS ELIMINADOS ── --}}
            @if(isset($deletedProducts) && $deletedProducts->count() > 0)
            <tr class="prod-sep" id="prod-deleted-sep">
                <td colspan="5">
                    <span class="prod-sep-label">🗑️ Sin stock / eliminados ({{ $deletedProducts->count() }})</span>
                </td>
            </tr>
            @foreach($deletedProducts as $product)
            @php
                $sc = $product->stock == 0 ? 'zero' : ($product->stock < 5 ? 'medium' : 'high');
                $sl = match($sc){'zero'=>'Sin stock','medium'=>$product->stock.' (Bajo)','high'=>$product->stock.' (OK)'};
            @endphp
            <tr class="prod-deleted">
                <td><span class="prank">–</span></td>
                <td>
                    <div class="pname">{{ $product->name }} <span class="pbadge-del">Eliminado</span></div>
                    @if($product->description)<div class="pdesc">{{ Str::limit($product->description,50) }}</div>@endif
                </td>
                <td><span class="pprice">${{ number_format($product->price,2,',','.') }}</span></td>
                <td><span class="pstock {{ $sc }}"><span class="psdot"></span>{{ $sl }}</span></td>
                <td></td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    @endif
</div>

{{-- ── MODAL: EDITAR ── --}}
<div class="pmodal-overlay" id="prodEditModal">
    <div class="pmodal">
        <div class="pmodal-title">✏️ Editar Producto</div>
        <form id="prodEditForm" method="POST">
            @csrf @method('PUT')
            <label for="pe-name">Nombre</label>
            <input type="text" id="pe-name" name="name" required maxlength="255" placeholder="Nombre del producto">
            <label for="pe-desc">Descripción (opcional)</label>
            <textarea id="pe-desc" name="description" maxlength="1000" placeholder="Descripción..."></textarea>
            <div class="pmodal-row">
                <div>
                    <label for="pe-price">Precio ($)</label>
                    <div class="pmodal-prefix-wrap">
                        <span class="pmodal-prefix">$</span>
                        <input type="number" id="pe-price" name="price" min="0" step="0.01" required placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label for="pe-stock">Stock</label>
                    <input type="number" id="pe-stock" name="stock" min="0" required placeholder="0">
                </div>
            </div>
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdEdit()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: STOCK ── --}}
<div class="pmodal-overlay" id="prodStockModal">
    <div class="pmodal">
        <div class="pmodal-title">📦 Actualizar Stock</div>
        <p class="pmodal-sub" id="pstock-label"></p>
        <form id="prodStockForm" method="POST">
            @csrf @method('PATCH')
            <label for="ps-qty">Nuevo stock (unidades)</label>
            <input type="number" id="ps-qty" name="stock" min="0" required>
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdStock()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: ELIMINAR ── --}}
<div class="pmodal-overlay" id="prodDelModal">
    <div class="pmodal">
        <div class="pmodal-title">🗑️ Confirmar Eliminación</div>
        <p class="pmodal-sub" id="pdel-label"></p>
        <form id="prodDelForm" method="POST">
            @csrf @method('DELETE')
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdDel()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-danger">Sí, eliminar</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: NUEVO PRODUCTO ── --}}
<div class="pmodal-overlay" id="prodNewModal">
    <div class="pmodal">
        <div class="pmodal-title">&#43; Nuevo Producto</div>
        <form id="prodNewForm" method="POST" action="/products">
            @csrf
            <label for="pn-name">Nombre</label>
            <input type="text" id="pn-name" name="name" required maxlength="255" placeholder="Nombre del producto">
            <label for="pn-desc">Descripción (opcional)</label>
            <textarea id="pn-desc" name="description" maxlength="1000" placeholder="Descripción..."></textarea>
            <div class="pmodal-row">
                <div>
                    <label for="pn-price">Precio ($)</label>
                    <div class="pmodal-prefix-wrap">
                        <span class="pmodal-prefix">$</span>
                        <input type="number" id="pn-price" name="price" min="0" step="0.01" required placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label for="pn-stock">Stock inicial</label>
                    <input type="number" id="pn-stock" name="stock" min="0" required placeholder="0">
                </div>
            </div>
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdNew()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm">✅ Crear Producto</button>
            </div>
        </form>
    </div>
</div>

{{-- ── MODAL: REPONER ELIMINADOS ── --}}
<div class="pmodal-overlay" id="prodRestoreModal">
    <div class="pmodal">
        <div class="pmodal-title">&#9851; Reponer Stock Eliminado</div>
        <p class="pmodal-sub">Seleccioná un producto eliminado y definí el nuevo stock para reactivarlo en el sistema.</p>
        <form id="prodRestoreForm" method="POST">
            @csrf @method('PATCH')
            <label for="pr-select">Producto eliminado</label>
            <select id="pr-select" required onchange="updateRestoreAction(this.value)">
                <option value="">— Seleccionar —</option>
                @if(isset($deletedProducts))
                @foreach($deletedProducts as $dp)
                <option value="{{ $dp->id }}">{{ $dp->name }}</option>
                @endforeach
                @endif
            </select>
            <label for="pr-stock">Nuevo stock (unidades)</label>
            <input type="number" id="pr-stock" name="stock" min="0" required placeholder="0">
            <div class="pmodal-footer">
                <button type="button" class="pbtn pbtn-cancel" onclick="closeProdRestore()">Cancelar</button>
                <button type="submit" class="pbtn pbtn-confirm" id="pr-submit" disabled>&#9851; Reponer</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Live search ──
document.getElementById('prod-search-input').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    const activeFilter = document.querySelector('.ps-filter-btn.active')?.getAttribute('onclick').match(/'([^']+)'/)[1] || 'all';
    
    document.querySelectorAll('#prod-table tbody tr').forEach(r => {
        if (r.classList.contains('prod-sep')) { r.style.display = ''; return; }
        
        const name = r.querySelector('.pname')?.textContent.toLowerCase() ?? '';
        const matchesSearch = name.includes(q);
        const status = r.getAttribute('data-stock-status');
        const matchesFilter = activeFilter === 'all' || status === activeFilter;
        
        r.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
});

// ── Filtro por Stock ──
function filterByStock(status, btn) {
    // UI update
    document.querySelectorAll('.ps-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const q = document.getElementById('prod-search-input').value.toLowerCase();
    
    document.querySelectorAll('#prod-table tbody tr').forEach(r => {
        if (r.classList.contains('prod-sep')) {
            r.style.display = (status === 'all' || status === 'zero') ? '' : 'none';
            return;
        }
        if (r.classList.contains('prod-deleted')) {
            r.style.display = (status === 'all' || status === 'zero') ? '' : 'none';
            return;
        }
        
        const name = r.querySelector('.pname')?.textContent.toLowerCase() ?? '';
        const matchesSearch = name.includes(q);
        const rowStatus = r.getAttribute('data-stock-status');
        const matchesFilter = status === 'all' || rowStatus === status;
        
        r.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}

// ── Edit ──
function openProdEdit(id, name, desc, price, stock){
    document.getElementById('pe-name').value  = name;
    document.getElementById('pe-desc').value  = desc;
    document.getElementById('pe-price').value = price;
    document.getElementById('pe-stock').value = stock;
    document.getElementById('prodEditForm').action = '/products/' + id;
    document.getElementById('prodEditModal').classList.add('open');
}
function closeProdEdit(){ document.getElementById('prodEditModal').classList.remove('open'); }

// ── Stock ──
function openProdStock(id, name, stock){
    document.getElementById('pstock-label').innerHTML = 'Modificando: <strong>' + name + '</strong>';
    document.getElementById('ps-qty').value = stock;
    document.getElementById('prodStockForm').action = '/products/' + id + '/stock';
    document.getElementById('prodStockModal').classList.add('open');
    document.getElementById('ps-qty').focus();
}
function closeProdStock(){ document.getElementById('prodStockModal').classList.remove('open'); }

// ── Delete ──
function openProdDelete(id, name){
    document.getElementById('pdel-label').innerHTML = 'Estás por eliminar <strong>' + name + '</strong>. Será desactivado del sistema.';
    document.getElementById('prodDelForm').action = '/products/' + id;
    document.getElementById('prodDelModal').classList.add('open');
}
function closeProdDel(){ document.getElementById('prodDelModal').classList.remove('open'); }

// ── Cerrar con overlay click / Escape ──
['prodEditModal','prodStockModal','prodDelModal','prodNewModal','prodRestoreModal'].forEach(id => {
    const el = document.getElementById(id);
    if(el) el.addEventListener('click', e => { if(e.target === el) el.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
    if(e.key === 'Escape'){
        closeProdEdit(); closeProdStock(); closeProdDel(); closeProdNew(); closeProdRestore();
    }
});

// ── Nuevo Producto ──
function openProdNew(){
    document.getElementById('prodNewForm').reset();
    document.getElementById('prodNewModal').classList.add('open');
    document.getElementById('pn-name').focus();
}
function closeProdNew(){ document.getElementById('prodNewModal').classList.remove('open'); }

// ── Reponer Eliminados ──
function openProdRestore(){
    document.getElementById('prodRestoreForm').reset();
    document.getElementById('pr-submit').disabled = true;
    document.getElementById('prodRestoreModal').classList.add('open');
}
function closeProdRestore(){ document.getElementById('prodRestoreModal').classList.remove('open'); }
function updateRestoreAction(id){
    if(id){
        document.getElementById('prodRestoreForm').action = '/products/' + id + '/restore';
        document.getElementById('pr-submit').disabled = false;
    } else {
        document.getElementById('pr-submit').disabled = true;
    }
}

// ── Toast desde session ──
@if(session('success'))
    (function(){
        const t = document.createElement('div');
        t.textContent = @json(session('success'));
        t.style.cssText = 'position:fixed;bottom:2rem;right:2rem;background:rgba(34,211,160,.15);color:#22d3a0;border:1px solid rgba(34,211,160,.4);padding:.85rem 1.4rem;border-radius:10px;font-size:.9rem;font-weight:500;z-index:999;transition:all .3s;max-width:340px;';
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
