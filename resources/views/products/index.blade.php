<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Productos – Kiosko UNER</title>
    <meta name="description" content="Módulo de gestión y visualización de productos del Kiosko UNER.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:           #0f1117;
            --bg-card:      #1a1d27;
            --bg-card2:     #22263a;
            --accent:       #6c63ff;
            --accent-light: #8b85ff;
            --green:        #22d3a0;
            --yellow:       #f5c842;
            --red:          #ff6b6b;
            --text:         #e8eaf0;
            --muted:        #8990a8;
            --border:       #2e3250;
            --radius:       12px;
            --shadow:       0 4px 24px rgba(0,0,0,0.45);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ─── HEADER ─── */
        .top-bar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }
        .top-bar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text);
        }
        .top-bar-brand .icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .breadcrumb {
            font-size: .82rem;
            color: var(--muted);
        }
        .breadcrumb span { color: var(--accent-light); font-weight: 500; }

        /* ─── MAIN CONTENT ─── */
        .main {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
        }

        /* ─── PAGE HEADER ─── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-title p {
            color: var(--muted);
            font-size: .88rem;
            margin-top: .25rem;
        }

        /* ─── STATS CARDS ─── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .4rem;
            transition: transform .2s, border-color .2s;
        }
        .stat-card:hover { transform: translateY(-3px); border-color: var(--accent); }
        .stat-card .label { font-size: .78rem; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
        .stat-card .value { font-size: 1.8rem; font-weight: 800; }
        .stat-card .value.green { color: var(--green); }
        .stat-card .value.yellow { color: var(--yellow); }
        .stat-card .value.red { color: var(--red); }
        .stat-card .value.purple { color: var(--accent-light); }

        /* ─── TOOLBAR ─── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .search-box svg {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }
        .search-box input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .6rem .9rem .6rem 2.4rem;
            color: var(--text);
            font-family: inherit;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s;
        }
        .search-box input:focus { border-color: var(--accent); }
        .search-box input::placeholder { color: var(--muted); }

        /* ─── TABLE ─── */
        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .table-header {
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .table-header h2 {
            font-size: 1rem;
            font-weight: 600;
        }
        .products-count {
            font-size: .82rem;
            color: var(--muted);
            background: var(--bg-card2);
            padding: .25rem .75rem;
            border-radius: 20px;
            border: 1px solid var(--border);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: var(--bg-card2);
            padding: .85rem 1.25rem;
            text-align: left;
            font-size: .78rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            white-space: nowrap;
        }
        thead th:first-child { padding-left: 1.75rem; }
        thead th:last-child  { padding-right: 1.75rem; text-align: right; }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(108,99,255,.06); }
        tbody td {
            padding: 1rem 1.25rem;
            font-size: .9rem;
            vertical-align: middle;
        }
        tbody td:first-child { padding-left: 1.75rem; }
        tbody td:last-child  { padding-right: 1.75rem; text-align: right; }

        /* ─── PRODUCT NAME ─── */
        .product-name {
            font-weight: 600;
            color: var(--text);
        }
        .product-desc {
            font-size: .78rem;
            color: var(--muted);
            margin-top: .2rem;
        }

        /* ─── RANK BADGE ─── */
        .rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px; height: 28px;
            border-radius: 50%;
            font-size: .78rem;
            font-weight: 700;
            background: var(--bg-card2);
            border: 1px solid var(--border);
            color: var(--muted);
        }
        .rank.gold   { background: rgba(245,200,66,.15); border-color: var(--yellow); color: var(--yellow); }
        .rank.silver { background: rgba(180,180,190,.15); border-color: #aaa; color: #ccc; }
        .rank.bronze { background: rgba(180,100,60,.15); border-color: #c87941; color: #c87941; }

        /* ─── PRICE ─── */
        .price {
            font-weight: 700;
            font-size: .98rem;
            color: var(--green);
        }

        /* ─── STOCK BADGE ─── */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .2rem .7rem;
            border-radius: 20px;
            font-size: .82rem;
            font-weight: 600;
        }
        .stock-badge.high   { background: rgba(34,211,160,.1); color: var(--green); border: 1px solid rgba(34,211,160,.3); }
        .stock-badge.medium { background: rgba(245,200,66,.1); color: var(--yellow); border: 1px solid rgba(245,200,66,.3); }
        .stock-badge.low    { background: rgba(255,107,107,.1); color: var(--red); border: 1px solid rgba(255,107,107,.3); }
        .stock-badge.zero   { background: rgba(255,107,107,.15); color: var(--red); border: 1px solid var(--red); }
        .stock-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }

        /* ─── ACTION BUTTONS ─── */
        .actions { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; flex-wrap: nowrap; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .45rem .85rem;
            border-radius: 8px;
            font-family: inherit;
            font-size: .82rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .18s;
            white-space: nowrap;
        }
        .btn-edit {
            background: rgba(108,99,255,.15);
            color: var(--accent-light);
            border: 1px solid rgba(108,99,255,.3);
        }
        .btn-edit:hover { background: rgba(108,99,255,.3); }

        .btn-stock {
            background: rgba(34,211,160,.1);
            color: var(--green);
            border: 1px solid rgba(34,211,160,.25);
        }
        .btn-stock:hover { background: rgba(34,211,160,.25); }

        .btn-delete {
            background: rgba(255,107,107,.1);
            color: var(--red);
            border: 1px solid rgba(255,107,107,.25);
        }
        .btn-delete:hover { background: rgba(255,107,107,.25); }

        /* ─── EMPTY STATE ─── */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-state .icon { font-size: 3.5rem; margin-bottom: 1rem; opacity: .4; }
        .empty-state p { color: var(--muted); font-size: .95rem; }

        /* ─── TOAST ─── */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            padding: .85rem 1.4rem;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 500;
            z-index: 999;
            opacity: 0;
            transform: translateY(20px);
            transition: all .3s;
            pointer-events: none;
            max-width: 360px;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { background: rgba(34,211,160,.15); color: var(--green); border: 1px solid rgba(34,211,160,.4); }
        .toast.error   { background: rgba(255,107,107,.15); color: var(--red);   border: 1px solid rgba(255,107,107,.4); }

        /* ─── MODAL ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.7);
            z-index: 200;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.6);
            animation: modalIn .25s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(.93) translateY(12px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }
        .modal label {
            display: block;
            font-size: .82rem;
            color: var(--muted);
            font-weight: 500;
            margin-bottom: .35rem;
        }
        .modal input[type=number] {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .65rem 1rem;
            color: var(--text);
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            margin-bottom: 1.25rem;
            transition: border-color .2s;
        }
        .modal input[type=number]:focus { border-color: var(--accent); }
        .modal-footer { display: flex; gap: .75rem; justify-content: flex-end; }
        .btn-cancel {
            background: var(--bg-card2);
            color: var(--muted);
            border: 1px solid var(--border);
        }
        .btn-confirm {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: #fff;
            border: none;
        }
        .btn-confirm:hover { opacity: .88; }

        /* ─── DELETE CONFIRM ─── */
        .delete-confirm-text {
            color: var(--muted);
            font-size: .9rem;
            margin-bottom: 1.25rem;
            line-height: 1.6;
        }
        .delete-confirm-text strong { color: var(--text); }
        .btn-danger {
            background: linear-gradient(135deg, #ff4757, var(--red));
            color: #fff;
            border: none;
        }
        .btn-danger:hover { opacity: .85; }

        @media (max-width: 768px) {
            .main { padding: 1.5rem 1rem; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .table-card { overflow-x: auto; }
        }
    </style>
</head>
<body>

{{-- ─── TOP BAR ─── --}}
<header class="top-bar">
    <div class="top-bar-brand">
        <div class="icon">🛍️</div>
        <span>Kiosko UNER</span>
    </div>
    <div class="breadcrumb">Panel / <span>Productos</span></div>
    <div class="top-bar-right">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-delete" style="padding:.4rem .85rem;font-size:.82rem;">
                ↩ Salir
            </button>
        </form>
    </div>
</header>

<main class="main">

    {{-- ─── PAGE HEADER ─── --}}
    <div class="page-header">
        <div class="page-title">
            <h1>Gestión de Productos</h1>
            <p>Lista ordenada por precio (mayor a menor) · Solo lectura de ingresos (a cargo de Compras)</p>
        </div>
    </div>

    {{-- ─── STAT CARDS ─── --}}
    @php
        $total    = $products->count();
        $sinStock = $products->where('stock', 0)->count();
        $stockBajo = $products->where('stock', '>', 0)->where('stock', '<=', 5)->count();
        $maxPrice = $products->max('price');
    @endphp
    <div class="stats-row">
        <div class="stat-card">
            <span class="label">Total Productos</span>
            <span class="value purple">{{ $total }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Con Stock Normal</span>
            <span class="value green">{{ $products->where('stock', '>', 5)->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Stock Bajo (≤5)</span>
            <span class="value yellow">{{ $stockBajo }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Sin Stock</span>
            <span class="value red">{{ $sinStock }}</span>
        </div>
        <div class="stat-card">
            <span class="label">Precio Más Alto</span>
            <span class="value green">
                @if($maxPrice) ${{ number_format($maxPrice, 2, ',', '.') }} @else – @endif
            </span>
        </div>
    </div>

    {{-- ─── TOOLBAR ─── --}}
    <div class="toolbar">
        <form method="GET" action="{{ route('products.index') }}" style="flex:1;display:flex;gap:.75rem;flex-wrap:wrap;">
            <div class="search-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input id="search-input" type="text" name="search" placeholder="Buscar producto..."
                       value="{{ request('search') }}" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-edit">Buscar</button>
            @if(request('search'))
                <a href="{{ route('products.index') }}" class="btn btn-cancel">✕ Limpiar</a>
            @endif
        </form>
    </div>

    {{-- ─── TABLE CARD ─── --}}
    <div class="table-card">
        <div class="table-header">
            <h2>📦 Productos Activos</h2>
            <span class="products-count">{{ $total }} {{ $total === 1 ? 'producto' : 'productos' }}</span>
        </div>

        @if($products->isEmpty())
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No hay productos para mostrar.</p>
            </div>
        @else
        <table id="products-table">
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
                    if     ($i === 0) $rankClass = 'gold';
                    elseif ($i === 1) $rankClass = 'silver';
                    elseif ($i === 2) $rankClass = 'bronze';
                    else              $rankClass = '';

                    if     ($product->stock === 0)  $stockClass = 'zero';
                    elseif ($product->stock <= 5)   $stockClass = 'low';
                    elseif ($product->stock <= 20)  $stockClass = 'medium';
                    else                             $stockClass = 'high';

                    $stockLabel = match($stockClass) {
                        'zero'   => 'Sin stock',
                        'low'    => $product->stock . ' (Bajo)',
                        'medium' => $product->stock . ' (Medio)',
                        'high'   => $product->stock . ' (OK)',
                    };
                @endphp
                <tr id="row-{{ $product->id }}">
                    {{-- Rank --}}
                    <td><span class="rank {{ $rankClass }}">{{ $i + 1 }}</span></td>

                    {{-- Name / Desc --}}
                    <td>
                        <div class="product-name">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="product-desc">{{ Str::limit($product->description, 55) }}</div>
                        @endif
                    </td>

                    {{-- Price --}}
                    <td><span class="price">${{ number_format($product->price, 2, ',', '.') }}</span></td>

                    {{-- Stock --}}
                    <td>
                        <span class="stock-badge {{ $stockClass }}">
                            <span class="stock-dot"></span>
                            {{ $stockLabel }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="actions">
                            {{-- Edit --}}
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-edit" title="Editar producto">
                                ✏️ Editar
                            </a>

                            {{-- Update Stock --}}
                            <button class="btn btn-stock" title="Actualizar stock"
                                    onclick="openStockModal({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})">
                                📦 Stock
                            </button>

                            {{-- Delete --}}
                            <button class="btn btn-delete" title="Eliminar producto"
                                    onclick="openDeleteModal({{ $product->id }}, '{{ addslashes($product->name) }}')">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</main>

{{-- ─── STOCK MODAL ─── --}}
<div class="modal-overlay" id="stockModal">
    <div class="modal">
        <div class="modal-title">📦 Actualizar Stock</div>
        <p class="delete-confirm-text" id="stockModalProductName"></p>
        <form id="stockForm" method="POST">
            @csrf
            @method('PATCH')
            <label for="stock-input">Nuevo stock (unidades)</label>
            <input type="number" id="stock-input" name="stock" min="0" required>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeStockModal()">Cancelar</button>
                <button type="submit" class="btn btn-confirm">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── DELETE MODAL ─── --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-title">🗑️ Confirmar Eliminación</div>
        <p class="delete-confirm-text" id="deleteModalText"></p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── TOAST ─── --}}
<div class="toast" id="toast"></div>

<script>
    // ── Toast ──
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.className   = 'toast ' + type + ' show';
        setTimeout(() => { t.className = 'toast'; }, 4000);
    }

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif

    // ── Stock Modal ──
    function openStockModal(id, name, currentStock) {
        document.getElementById('stockModalProductName').innerHTML =
            'Modificando stock de: <strong>' + name + '</strong>';
        document.getElementById('stock-input').value = currentStock;
        document.getElementById('stockForm').action  = '/products/' + id + '/stock';
        document.getElementById('stockModal').classList.add('open');
        document.getElementById('stock-input').focus();
    }
    function closeStockModal() {
        document.getElementById('stockModal').classList.remove('open');
    }

    // ── Delete Modal ──
    function openDeleteModal(id, name) {
        document.getElementById('deleteModalText').innerHTML =
            'Estás por eliminar <strong>' + name + '</strong>. Esta acción lo desactivará del sistema.';
        document.getElementById('deleteForm').action = '/products/' + id;
        document.getElementById('deleteModal').classList.add('open');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
    }

    // Close modals on overlay click
    document.getElementById('stockModal').addEventListener('click', function(e) {
        if (e.target === this) closeStockModal();
    });
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Escape key closes modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeStockModal(); closeDeleteModal(); }
    });

    // Live search filter (client-side)
    document.getElementById('search-input').addEventListener('input', function() {
        const q   = this.value.toLowerCase();
        const rows = document.querySelectorAll('#products-table tbody tr');
        rows.forEach(row => {
            const name = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(q) ? '' : 'none';
        });
    });
</script>
</body>
</html>
