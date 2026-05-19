<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Producto – Kiosko UNER</title>
    <meta name="description" content="Edición de producto en el Kiosko UNER.">
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

        /* ─── TOP BAR ─── */
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
        }
        .top-bar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .top-bar-brand .icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .breadcrumb { font-size: .82rem; color: var(--muted); }
        .breadcrumb a { color: var(--accent-light); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: var(--muted); }

        /* ─── MAIN ─── */
        .main {
            max-width: 680px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
        }

        /* ─── PAGE HEADER ─── */
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 1.7rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--text), var(--accent-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-header p {
            color: var(--muted);
            font-size: .88rem;
            margin-top: .3rem;
        }

        /* ─── FORM CARD ─── */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2.25rem;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group:last-child { margin-bottom: 0; }

        label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .5rem;
        }

        input[type=text],
        input[type=number],
        textarea {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .8rem 1rem;
            color: var(--text);
            font-family: inherit;
            font-size: .95rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input[type=text]:focus,
        input[type=number]:focus,
        textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(108,99,255,.15);
        }
        textarea {
            resize: vertical;
            min-height: 110px;
        }
        input::placeholder, textarea::placeholder { color: var(--muted); }

        /* ─── PRICE / STOCK ROW ─── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        /* ─── INPUT PREFIX ─── */
        .input-wrapper {
            position: relative;
        }
        .input-prefix {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-weight: 600;
            font-size: .95rem;
            pointer-events: none;
        }
        .input-wrapper input { padding-left: 2rem; }

        /* ─── ERROR ─── */
        .error-msg {
            color: var(--red);
            font-size: .8rem;
            margin-top: .35rem;
        }

        /* ─── DIVIDER ─── */
        .divider {
            border: none;
            border-top: 1px solid var(--border);
            margin: 1.75rem 0;
        }

        /* ─── FORM FOOTER ─── */
        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .85rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .65rem 1.4rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-cancel {
            background: var(--bg-card2);
            color: var(--muted);
            border: 1px solid var(--border);
        }
        .btn-cancel:hover { border-color: var(--muted); color: var(--text); }

        .btn-save {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: #fff;
            box-shadow: 0 4px 16px rgba(108,99,255,.35);
        }
        .btn-save:hover { opacity: .88; transform: translateY(-1px); }

        /* ─── CURRENT INFO BADGE ─── */
        .current-info {
            display: flex;
            gap: 1rem;
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: .85rem 1.1rem;
            margin-bottom: 1.75rem;
            font-size: .85rem;
            color: var(--muted);
            flex-wrap: wrap;
        }
        .current-info strong { color: var(--text); }

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
        }
        .toast.show  { opacity: 1; transform: translateY(0); }
        .toast.error { background: rgba(255,107,107,.15); color: var(--red); border: 1px solid rgba(255,107,107,.4); }

        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; }
            .main { padding: 2rem 1rem; }
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
    <div class="breadcrumb">
        <a href="{{ route('products.index') }}">Productos</a>
        <span> / Editar</span>
    </div>
</header>

<main class="main">

    <div class="page-header">
        <h1>✏️ Editar Producto</h1>
        <p>Modificá el nombre, descripción, precio o stock del producto.</p>
    </div>

    {{-- Info del producto actual --}}
    <div class="current-info">
        <span>🆔 ID: <strong>{{ $product->id }}</strong></span>
        <span>💰 Precio actual: <strong>${{ number_format($product->price, 2, ',', '.') }}</strong></span>
        <span>📦 Stock actual: <strong>{{ $product->stock }} u.</strong></span>
        <span>📅 Creado: <strong>{{ $product->created_at->format('d/m/Y') }}</strong></span>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('products.update', $product) }}" id="edit-form">
            @csrf
            @method('PUT')

            {{-- Nombre --}}
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    placeholder="Ej: Coca-Cola 2.25L"
                    required
                    maxlength="255"
                    autocomplete="off"
                >
                @error('name')
                    <p class="error-msg">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div class="form-group">
                <label for="description">Descripción (opcional)</label>
                <textarea
                    id="description"
                    name="description"
                    placeholder="Descripción breve del producto..."
                    maxlength="1000"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="error-msg">⚠ {{ $message }}</p>
                @enderror
            </div>

            {{-- Precio y Stock --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Precio ($)</label>
                    <div class="input-wrapper">
                        <span class="input-prefix">$</span>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price', $product->price) }}"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                        >
                    </div>
                    @error('price')
                        <p class="error-msg">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="stock">Stock (unidades)</label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        value="{{ old('stock', $product->stock) }}"
                        placeholder="0"
                        min="0"
                        required
                    >
                    @error('stock')
                        <p class="error-msg">⚠ {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <hr class="divider">

            <div class="form-footer">
                <a href="{{ route('products.index') }}" class="btn btn-cancel">← Cancelar</a>
                <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
            </div>
        </form>
    </div>

</main>

{{-- ─── TOAST ─── --}}
<div class="toast" id="toast"></div>

<script>
    @if($errors->any())
        const t = document.getElementById('toast');
        t.textContent = 'Corregí los errores antes de guardar.';
        t.className   = 'toast error show';
        setTimeout(() => { t.className = 'toast error'; }, 4500);
    @endif
</script>
</body>
</html>
