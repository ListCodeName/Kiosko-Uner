<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Showcase UI – Kiosko UNER</title>
    <meta name="description" content="Catálogo de elementos UI del sistema Kiosko UNER. Tema oscuro con paleta azul neon.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/showcase/showcase.css') }}">
    <style>
        @keyframes ripple { to { transform: scale(3); opacity: 0; } }
        .palette-dot {
            width: 48px; height: 48px; border-radius: 8px; cursor: pointer;
            transition: transform .2s; position: relative;
        }
        .palette-dot:hover { transform: scale(1.12); }
        .palette-dot span {
            position: absolute; bottom: -20px; left: 50%; transform: translateX(-50%);
            font-size: 0.65rem; color: var(--text-muted); white-space: nowrap;
        }
        .palette-row { display: flex; gap: 12px; align-items: flex-end; padding-bottom: 24px; flex-wrap: wrap; }
        .color-swatch { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .color-swatch .hex { font-size: 0.68rem; color: var(--text-muted); }
    </style>
</head>
<body>

<div class="sc-wrapper">

    <!-- ENCABEZADO -->
    <header class="sc-header">
        <h1>🎨 Showcase de Elementos UI</h1>
        <p>Kiosko UNER · Tema oscuro · Paleta azul neon · CSS puro</p>
    </header>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 1. PALETA DE COLORES                                    -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">01 · Paleta de Colores</h2>
        <div class="palette-row">
            @php
            $palette = [
                ['#0d0f14','Base'],['#131720','Superficie'],['#1a2030','Card'],
                ['#111520','Input'],['#1a3a5c','Blue dim'],['#1e5fa8','Blue mid'],
                ['#2d8cff','Blue neon'],['#6ab4ff','Blue light'],
                ['#22c55e','Agregar'],['#f97316','Editar'],['#ef4444','Eliminar'],
            ];
            @endphp
            @foreach($palette as $c)
            <div class="color-swatch">
                <div class="palette-dot copy-btn"
                     data-copy="{{ $c[0] }}"
                     style="background:{{ $c[0] }}; border:1px solid rgba(255,255,255,0.1);"
                     title="Copiar {{ $c[0] }}"></div>
                <span class="hex">{{ $c[1] }}</span>
                <span class="hex">{{ $c[0] }}</span>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 2. BOTONES                                              -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">02 · Botones de Acción</h2>

        <p style="font-size:.78rem;color:var(--text-muted);margin-bottom:16px;">Hover sobre cada botón para ver el color de estado.</p>

        <div class="sc-col">
            <!-- Por función -->
            <div class="sc-row">
                <button class="btn btn-add">＋ Agregar</button>
                <button class="btn btn-edit">✏ Editar</button>
                <button class="btn btn-del">✕ Eliminar</button>
                <button class="btn btn-gen">⟳ Guardar</button>
                <button class="btn btn-ghost">◎ Ver más</button>
            </div>

            <!-- Tamaños -->
            <div class="sc-row">
                <button class="btn btn-gen btn-sm">Pequeño</button>
                <button class="btn btn-gen">Normal</button>
                <button class="btn btn-gen btn-lg">Grande</button>
            </div>

            <!-- Con iconos combinados -->
            <div class="sc-row">
                <button class="btn btn-add">＋ Nuevo producto</button>
                <button class="btn btn-edit">✏ Modificar stock</button>
                <button class="btn btn-del">🗑 Dar de baja</button>
                <button class="btn btn-gen" disabled>⊘ Bloqueado</button>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 3. CAMPOS DE TEXTO                                      -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">03 · Campos de Texto</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px;">

            <div class="form-group">
                <label class="form-label">Nombre de producto</label>
                <input type="text" class="form-input" placeholder="Ej: Medialunas x12">
                <span class="form-hint">Nombre visible en el kiosko</span>
            </div>

            <div class="form-group">
                <label class="form-label">Precio</label>
                <input type="number" class="form-input" placeholder="0.00">
            </div>

            <div class="form-group">
                <label class="form-label">Estado · Éxito</label>
                <input type="text" class="form-input input-success" value="Stock disponible">
                <span class="form-hint success">✔ Verificado</span>
            </div>

            <div class="form-group">
                <label class="form-label">Estado · Error</label>
                <input type="text" class="form-input input-error" value="–1 unidades">
                <span class="form-hint error">✖ Stock inválido</span>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-input" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label class="form-label">Descripción</label>
                <textarea class="form-textarea" placeholder="Descripción del ítem..."></textarea>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 4. SELECTORES                                           -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">04 · Selectores</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;">

            <div class="form-group">
                <label class="form-label">Categoría</label>
                <select class="form-select">
                    <option value="">— Seleccioná —</option>
                    <option>Panadería</option>
                    <option>Bebidas</option>
                    <option>Snacks</option>
                    <option>Lácteos</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Turno de atención</label>
                <select class="form-select">
                    <option>Mañana (08:00–12:00)</option>
                    <option>Tarde (12:00–18:00)</option>
                    <option>Noche (18:00–22:00)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Estado de pedido</label>
                <select class="form-select">
                    <option>Pendiente</option>
                    <option>En preparación</option>
                    <option>Listo</option>
                    <option>Entregado</option>
                </select>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 5. RADIO BUTTONS & CHECKBOXES                          -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">05 · Radio Buttons y Checkboxes</h2>
        <div class="sc-row" style="align-items:flex-start;gap:48px;">

            <div>
                <p class="form-label" style="margin-bottom:12px;">Tipo de cliente</p>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="tipo_cliente" value="estudiante" checked>
                        <span class="radio-custom"></span>
                        Estudiante
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="tipo_cliente" value="docente">
                        <span class="radio-custom"></span>
                        Docente
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="tipo_cliente" value="externo">
                        <span class="radio-custom"></span>
                        Externo
                    </label>
                </div>
            </div>

            <div>
                <p class="form-label" style="margin-bottom:12px;">Preferencias de aviso</p>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" checked>
                        <span class="checkbox-custom"></span>
                        Notificar cuando el pedido esté listo
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox">
                        <span class="checkbox-custom"></span>
                        Recibir resumen diario
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" checked>
                        <span class="checkbox-custom"></span>
                        Alertas de stock bajo
                    </label>
                </div>
            </div>

            <div>
                <p class="form-label" style="margin-bottom:12px;">Opciones de sistema</p>
                <div class="sc-col" style="gap:14px;">
                    <label class="toggle-label">
                        <input type="checkbox" checked>
                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                        Kiosko activo
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox">
                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                        Modo mantenimiento
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox" checked>
                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                        Registro de ventas
                    </label>
                </div>
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 6. SELECTOR DE ÍTEMS (TILES)                           -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">06 · Selector de Ítems</h2>
        <p class="form-hint" style="margin-bottom:16px;">Hacé clic para seleccionar un ítem.</p>
        <div class="item-grid">
            @php
            $items = [
                ['🥐','Medialunas'],['☕','Café'],['🥤','Gaseosa'],
                ['🍕','Porción'],['🧁','Muffin'],['🍪','Galletitas'],
                ['🥛','Leche'],['🧃','Jugo'],['🍫','Chocolate'],
            ];
            @endphp
            @foreach($items as $item)
            <div class="item-tile {{ $loop->first ? 'selected' : '' }}">
                <div class="item-tile-icon">{{ $item[0] }}</div>
                <div class="item-tile-label">{{ $item[1] }}</div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 7. RANGE SLIDER & PROGRESS                              -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">07 · Slider y Barra de Progreso</h2>
        <div class="sc-col">

            <div class="form-group">
                <label class="form-label">
                    Stock mínimo de alerta:
                    <strong id="range-val" style="color:var(--blue-neon);">15</strong> unidades
                </label>
                <input type="range" class="form-range" min="0" max="100" value="15"
                       data-output="range-val">
            </div>

            <div>
                <p class="form-label" style="margin-bottom:10px;">Capacidad de pedidos ocupada — 68%</p>
                <div class="progress-bar">
                    <div class="progress-fill" data-value="68" style="width:0%"></div>
                </div>
            </div>

            <div>
                <p class="form-label" style="margin-bottom:10px;">Stock disponible — 32%</p>
                <div class="progress-bar">
                    <div class="progress-fill" data-value="32" style="width:0%"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 8. BADGES                                               -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">08 · Badges / Chips de Estado</h2>
        <div class="sc-row">
            <span class="badge badge-blue">● Activo</span>
            <span class="badge badge-green">✔ Entregado</span>
            <span class="badge badge-orange">⚡ En preparación</span>
            <span class="badge badge-red">✖ Cancelado</span>
            <span class="badge badge-blue">★ Destacado</span>
            <span class="badge badge-green">↑ Stock OK</span>
            <span class="badge badge-red">↓ Stock bajo</span>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 9. ALERTAS                                              -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">09 · Alertas del Sistema</h2>
        <div class="sc-col">
            <div class="alert alert-info">ℹ El kiosko cerrará a las 22:00 hs.</div>
            <div class="alert alert-success">✔ Pedido registrado correctamente.</div>
            <div class="alert alert-warning">⚠ Stock de café menor a 5 unidades.</div>
            <div class="alert alert-danger">✖ Error al procesar el pago. Reintentá.</div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 10. TABLA                                               -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">10 · Tabla de Datos</h2>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rows = [
                        [1,'Medialunas x6','Panadería',42,'Activo'],
                        [2,'Café con leche','Bebidas',8,'Activo'],
                        [3,'Gaseosa 500ml','Bebidas',0,'Sin stock'],
                        [4,'Alfajor triple','Snacks',17,'Activo'],
                    ];
                    @endphp
                    @foreach($rows as $r)
                    <tr>
                        <td style="color:var(--text-muted);">{{ $r[0] }}</td>
                        <td><strong>{{ $r[1] }}</strong></td>
                        <td>{{ $r[2] }}</td>
                        <td>
                            @if($r[3] === 0)
                                <span class="badge badge-red">{{ $r[3] }}</span>
                            @elseif($r[3] < 10)
                                <span class="badge badge-orange">{{ $r[3] }}</span>
                            @else
                                <span class="badge badge-green">{{ $r[3] }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $r[4] === 'Activo' ? 'badge-blue' : 'badge-red' }}">
                                {{ $r[4] }}
                            </span>
                        </td>
                        <td>
                            <div class="sc-row" style="gap:8px;">
                                <button class="btn btn-edit btn-sm">✏</button>
                                <button class="btn btn-del btn-sm">✕</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 11. CARDS                                               -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">11 · Cards de Contenido</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
            <div class="ui-card">
                <h3>📦 Inventario</h3>
                <p>Administrá productos, stock y categorías del kiosko.</p>
                <br>
                <button class="btn btn-gen btn-sm">Ver módulo</button>
            </div>
            <div class="ui-card">
                <h3>🛒 Pedidos</h3>
                <p>Registrá y gestioná los pedidos en tiempo real.</p>
                <br>
                <button class="btn btn-gen btn-sm">Ver módulo</button>
            </div>
            <div class="ui-card">
                <h3>👤 Usuarios</h3>
                <p>Alta, baja y modificación de usuarios del sistema.</p>
                <br>
                <button class="btn btn-add btn-sm">＋ Nuevo</button>
            </div>
            <div class="ui-card">
                <h3>📊 Reportes</h3>
                <p>Estadísticas de ventas, stock y actividad del día.</p>
                <br>
                <button class="btn btn-gen btn-sm">Exportar</button>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- 12. TOOLTIPS                                            -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="sc-section">
        <h2 class="sc-section-title">12 · Tooltips</h2>
        <div class="sc-row">
            <div class="tooltip-wrap">
                <button class="btn btn-add">＋ Agregar</button>
                <div class="tooltip-box">Crear un nuevo ítem en el kiosko</div>
            </div>
            <div class="tooltip-wrap">
                <button class="btn btn-edit">✏ Editar</button>
                <div class="tooltip-box">Modificar datos del ítem seleccionado</div>
            </div>
            <div class="tooltip-wrap">
                <button class="btn btn-del">✕ Eliminar</button>
                <div class="tooltip-box">Dar de baja el ítem permanentemente</div>
            </div>
        </div>
    </section>

</div><!-- /.sc-wrapper -->

<script src="{{ asset('js/showcase/showcase.js') }}"></script>
</body>
</html>
