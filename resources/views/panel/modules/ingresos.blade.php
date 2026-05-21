{{-- MÓDULO: INGRESOS --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <div class="mov-summary" id="ingresosSummary">
        <span class="summary-badge" id="ingresosTotalCount">0 registros</span>
        <span class="summary-badge highlight-green" id="ingresosTotalMonto">$0,00 total</span>
        <span class="summary-badge" id="ingresosPendienteCount">0 pendientes</span>
    </div>
    <div style="flex:1"></div>
    <div class="mov-filter-wrap">
        <select class="mov-filter-select" id="ingresosFilterEstado">
            <option value="">Todos</option>
            <option value="efectuado">Efectuados</option>
            <option value="pendiente">Pendientes</option>
        </select>
        <select class="mov-filter-select" id="ingresosFilterTipo">
            <option value="">Todas las categorías</option>
            <option value="activo_no_comestible">Activo no comestible</option>
            <option value="excedente_caja">Excedente de caja</option>
            <option value="donacion">Donación</option>
            <option value="subvencion">Subvención</option>
            <option value="ingreso_excepcional">Ingreso excepcional</option>
            <option value="otro">Otro</option>
        </select>
    </div>
    <button class="btn btn-gen btn-ingreso-add" id="btnNewIngreso">＋ Registrar Ingreso</button>
</div>

{{-- Tabla --}}
<div class="data-table-wrapper" id="ingresosTableWrapper">
    <table class="data-table" id="ingresosTable">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Monto</th>
                <th>Estado</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody id="ingresosTbody"></tbody>
    </table>
</div>

{{-- Empty state --}}
<div class="table-empty" id="ingresosEmpty" style="display:none">
    <div class="empty-state" style="min-height:300px">
        <div class="empty-state-illustration">
            <div class="empty-state-pulse"></div>
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polyline points="8,44 18,30 28,36 40,16 52,20" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.8" fill="none"/>
                <circle cx="40" cy="16" r="4" fill="#22c55e" opacity="0.9"/>
                <line x1="40" y1="16" x2="40" y2="8" stroke="#22c55e" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="36" y1="12" x2="40" y2="8" stroke="#22c55e" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="44" y1="12" x2="40" y2="8" stroke="#22c55e" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="8" y1="46" x2="52" y2="46" stroke="#22c55e" stroke-width="1" opacity="0.3"/>
            </svg>
        </div>
        <h2 class="empty-state-title">Sin ingresos registrados</h2>
        <p class="empty-state-desc">
            No hay movimientos de ingreso aún.<br>
            Registrá el primer ingreso para comenzar el seguimiento económico.
        </p>
        <button class="btn btn-gen btn-ingreso-add" id="btnNewIngresoEmpty">＋ Registrar ingreso</button>
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="ingresosToast"></div>

{{-- ══════════════════ MODAL: CREAR / EDITAR INGRESO ══════════════════ --}}
<div class="modal-overlay" id="ingresoFormModal">
    <div class="modal" style="max-width:560px">
        <div class="modal-header">
            <h3 class="modal-title" id="ingresoModalTitle">📈 Nuevo Ingreso</h3>
            <button class="modal-close" id="ingresoModalClose">✕</button>
        </div>
        <form class="modal-body" id="ingresoForm">
            <input type="hidden" id="ingreso-edit-id">

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Fecha <span style="color:#ef4444">*</span></label>
                    <input class="form-input" type="date" name="fecha" id="ingreso-fecha" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría <span style="color:#ef4444">*</span></label>
                    <select class="form-input" name="tipo" id="ingreso-tipo" required>
                        <option value="">Seleccionar...</option>
                        <option value="activo_no_comestible">Activo no comestible</option>
                        <option value="excedente_caja">Excedente de caja</option>
                        <option value="donacion">Donación</option>
                        <option value="subvencion">Subvención</option>
                        <option value="ingreso_excepcional">Ingreso excepcional</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción <span style="color:#ef4444">*</span></label>
                <input class="form-input" type="text" name="descripcion" id="ingreso-descripcion"
                       placeholder="Ej: Donación de la cooperadora..." required maxlength="200">
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Monto <span style="color:#ef4444">*</span></label>
                    <input class="form-input" type="number" name="monto" id="ingreso-monto"
                           step="0.01" min="0.01" placeholder="$0,00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Estado <span style="color:#ef4444">*</span></label>
                    <select class="form-input" name="estado" id="ingreso-estado" required>
                        <option value="efectuado">✅ Efectuado</option>
                        <option value="pendiente">🕐 Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Detalle / Observaciones</label>
                <textarea class="form-input" name="detalle" id="ingreso-detalle"
                          rows="3" placeholder="Información adicional sobre este ingreso..."
                          style="resize:vertical; min-height:72px"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="ingresoCancelBtn">Cancelar</button>
                <button type="submit" class="btn-submit btn-submit-green" id="ingresoSubmitBtn">Registrar Ingreso</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: ELIMINAR INGRESO ══════════════════ --}}
<div class="modal-overlay" id="ingresoDeleteModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <h3 class="modal-title">⚠️ Confirmar Eliminación</h3>
            <button class="modal-close" id="ingresoDelClose">✕</button>
        </div>
        <form class="modal-body" id="ingresoDeleteForm">
            <input type="hidden" id="ingreso-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">
                ¿Estás seguro de que querés eliminar este ingreso?
            </p>
            <div class="mov-del-preview" id="ingresoDelPreview"></div>
            <p style="color:var(--text-muted);font-size:.78rem;margin-top:12px">Esta acción no se puede deshacer.</p>
            <div class="modal-footer" style="margin-top:16px">
                <button type="button" class="btn-cancel" id="ingresoDelCancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ── Estado ── */
    let ingresosData = [];
    let loaded = false;
    let filterEstado = '';
    let filterTipo = '';

    /* ── Helpers ── */
    function formatMoney(v) {
        return '$' + Number(v).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(str) {
        if (!str) return '—';
        const [y, m, d] = str.split('-');
        return `${d}/${m}/${y}`;
    }

    const TIPO_LABELS = {
        activo_no_comestible: 'Activo no comestible',
        excedente_caja:       'Excedente de caja',
        donacion:             'Donación',
        subvencion:           'Subvención',
        ingreso_excepcional:  'Ingreso excepcional',
        otro:                 'Otro',
    };

    function tipoLabel(t) { return TIPO_LABELS[t] || t || '—'; }

    /* ── API Helper ── */
    async function api(url, method = 'GET', body = null) {
        const currentCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': currentCsrfToken } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res = await fetch(url, opts);
        const data = await res.json();
        if (!res.ok) throw data;
        return data;
    }

    /* ── Toast ── */
    function toast(msg, type = 'success') {
        const t = document.getElementById('ingresosToast');
        if (!t) return;
        t.textContent = msg;
        t.className = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ── Modales ── */
    // Gestionado de forma universal por modal-manager.js

    /* ── Render tabla ── */
    function filtered() {
        return ingresosData.filter(r => {
            if (filterEstado && r.estado !== filterEstado) return false;
            if (filterTipo   && r.tipo   !== filterTipo)   return false;
            return true;
        });
    }

    function renderTable() {
        const tbody   = document.getElementById('ingresosTbody');
        const wrapper = document.getElementById('ingresosTableWrapper');
        const empty   = document.getElementById('ingresosEmpty');
        const countBadge  = document.getElementById('ingresosTotalCount');
        const montoBadge  = document.getElementById('ingresosTotalMonto');
        const pendBadge   = document.getElementById('ingresosPendienteCount');
        if (!tbody) return;

        const all  = ingresosData;
        const rows = filtered();

        const totalMonto  = all.reduce((s, r) => s + parseFloat(r.monto || 0), 0);
        const pendientes  = all.filter(r => r.estado === 'pendiente').length;

        if (countBadge) countBadge.textContent = `${all.length} registro${all.length !== 1 ? 's' : ''}`;
        if (montoBadge) montoBadge.textContent = formatMoney(totalMonto) + ' total';
        if (pendBadge)  pendBadge.textContent  = `${pendientes} pendiente${pendientes !== 1 ? 's' : ''}`;

        if (!rows.length) {
            tbody.innerHTML = '';
            if (all.length === 0) {
                if (wrapper) wrapper.style.display = 'none';
                if (empty)   empty.style.display   = 'flex';
            } else {
                if (wrapper) wrapper.style.display = 'block';
                if (empty)   empty.style.display   = 'none';
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:36px">Sin resultados para el filtro seleccionado.</td></tr>`;
            }
            return;
        }

        if (wrapper) wrapper.style.display = 'block';
        if (empty)   empty.style.display   = 'none';

        tbody.innerHTML = rows.map(r => `
            <tr class="mov-row">
                <td style="white-space:nowrap;font-weight:600;color:var(--text-white)">${formatDate(r.fecha)}</td>
                <td>
                    <div style="font-weight:600;color:var(--text-primary);max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${r.descripcion}</div>
                    ${r.detalle ? `<div style="font-size:.75rem;color:var(--text-muted);margin-top:2px;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${r.detalle}</div>` : ''}
                </td>
                <td>
                    <span class="mov-tipo-badge mov-tipo-ingreso">${tipoLabel(r.tipo)}</span>
                </td>
                <td style="font-weight:700;color:#22c55e;white-space:nowrap">${formatMoney(r.monto)}</td>
                <td>
                    <span class="mov-estado-badge ${r.estado === 'efectuado' ? 'estado-efectuado' : 'estado-pendiente'}">
                        ${r.estado === 'efectuado' ? '✅ Efectuado' : '🕐 Pendiente'}
                    </span>
                </td>
                <td>
                    <div class="actions-cell" style="justify-content:center">
                        <button class="action-btn" title="Editar" onclick="INGRESOS.edit(${r.id})">✏️</button>
                        <button class="action-btn danger" title="Eliminar" onclick="INGRESOS.del(${r.id})">🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /* ── Cargar ── */
    async function load() {
        if (loaded) return;
        try {
            const data = await api('/panel/api/ingresos');
            ingresosData = data.ingresos || [];
            loaded = true;
            renderTable();
        } catch (e) {
            toast('Error al cargar ingresos desde el servidor', 'error');
        }
    }

    /* Observer de activación */
    const section = document.querySelector('[data-module-content="ingresos"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) load();
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
    }

    /* ── Filtros ── */
    document.getElementById('ingresosFilterEstado')?.addEventListener('change', function() {
        filterEstado = this.value;
        renderTable();
    });
    document.getElementById('ingresosFilterTipo')?.addEventListener('change', function() {
        filterTipo = this.value;
        renderTable();
    });

    /* ── Abrir modal nuevo ── */
    function openNewModal() {
        document.getElementById('ingresoForm')?.reset();
        document.getElementById('ingreso-edit-id').value = '';
        document.getElementById('ingresoModalTitle').textContent = '📈 Nuevo Ingreso';
        document.getElementById('ingresoSubmitBtn').textContent = 'Registrar Ingreso';
        // Fecha por defecto: hoy
        const today = new Date().toISOString().slice(0, 10);
        document.getElementById('ingreso-fecha').value = today;
        window.openModal('ingresoFormModal');
    }

    document.getElementById('btnNewIngreso')?.addEventListener('click', openNewModal);
    document.getElementById('btnNewIngresoEmpty')?.addEventListener('click', openNewModal);

    /* ── Editar ── */
    window.INGRESOS = window.INGRESOS || {};

    INGRESOS.edit = function(id) {
        const r = ingresosData.find(x => x.id === id);
        if (!r) return;
        document.getElementById('ingreso-edit-id').value = id;
        document.getElementById('ingreso-fecha').value       = r.fecha;
        document.getElementById('ingreso-tipo').value        = r.tipo;
        document.getElementById('ingreso-descripcion').value = r.descripcion;
        document.getElementById('ingreso-monto').value       = r.monto;
        document.getElementById('ingreso-estado').value      = r.estado;
        document.getElementById('ingreso-detalle').value     = r.detalle || '';
        document.getElementById('ingresoModalTitle').textContent = '✏️ Editar Ingreso';
        document.getElementById('ingresoSubmitBtn').textContent  = 'Guardar Cambios';
        window.openModal('ingresoFormModal');
    };

    /* ── Eliminar ── */
    INGRESOS.del = function(id) {
        const r = ingresosData.find(x => x.id === id);
        if (!r) return;
        document.getElementById('ingreso-del-id').value = id;
        document.getElementById('ingresoDelPreview').innerHTML = `
            <div class="mov-del-detail">
                <span class="mov-del-label">Descripción</span>
                <span>${r.descripcion}</span>
            </div>
            <div class="mov-del-detail">
                <span class="mov-del-label">Monto</span>
                <span style="color:#22c55e;font-weight:700">${formatMoney(r.monto)}</span>
            </div>
            <div class="mov-del-detail">
                <span class="mov-del-label">Fecha</span>
                <span>${formatDate(r.fecha)}</span>
            </div>
            <div class="mov-del-detail">
                <span class="mov-del-label">Estado</span>
                <span class="mov-estado-badge ${r.estado === 'efectuado' ? 'estado-efectuado' : 'estado-pendiente'}" style="font-size:.75rem">
                    ${r.estado === 'efectuado' ? '✅ Efectuado' : '🕐 Pendiente'}
                </span>
            </div>
        `;
        window.openModal('ingresoDeleteModal');
    };

    /* ── Submit form ── */
    document.getElementById('ingresoForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd  = new FormData(e.target);
        const id  = document.getElementById('ingreso-edit-id').value;
        const rec = {
            fecha:       fd.get('fecha'),
            tipo:        fd.get('tipo'),
            descripcion: fd.get('descripcion').trim(),
            monto:       parseFloat(fd.get('monto')),
            estado:      fd.get('estado'),
            detalle:     fd.get('detalle')?.trim() || '',
        };

        try {
            if (id) {
                await api(`/panel/api/ingresos/${id}`, 'PUT', rec);
                toast('Ingreso actualizado correctamente');
            } else {
                await api('/panel/api/ingresos', 'POST', rec);
                toast('Ingreso registrado exitosamente');
            }
            loaded = false;
            await load();
            window.closeModal('ingresoFormModal');
        } catch (err) {
            toast(err.message || 'Error al procesar el ingreso', 'error');
        }
    });

    /* ── Delete form ── */
    document.getElementById('ingresoDeleteForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('ingreso-del-id').value;
        try {
            await api(`/panel/api/ingresos/${id}`, 'DELETE');
            toast('Ingreso eliminado');
            loaded = false;
            await load();
            window.closeModal('ingresoDeleteModal');
        } catch (err) {
            toast('Error al eliminar el ingreso', 'error');
        }
    });

})();
</script>
