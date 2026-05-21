{{-- MÓDULO: EGRESOS --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <div class="mov-summary" id="egresosSummary">
        <span class="summary-badge" id="egresosTotalCount">0 registros</span>
        <span class="summary-badge highlight-red" id="egresosTotalMonto">$0,00 total</span>
        <span class="summary-badge" id="egresosPendienteCount">0 pendientes</span>
    </div>
    <div style="flex:1"></div>
    <div class="mov-filter-wrap">
        <select class="mov-filter-select" id="egresosFilterEstado">
            <option value="">Todos</option>
            <option value="efectuado">Efectuados</option>
            <option value="pendiente">Pendientes</option>
        </select>
        <select class="mov-filter-select" id="egresosFilterTipo">
            <option value="">Todas las categorías</option>
            <option value="gasto_operativo">Gasto operativo</option>
            <option value="pasivo">Pasivo / Deuda</option>
            <option value="insumos">Insumos</option>
            <option value="servicio">Servicios</option>
            <option value="impuesto">Impuesto / Tasa</option>
            <option value="otro">Otro</option>
        </select>
    </div>
    <button class="btn btn-gen btn-egreso-add" id="btnNewEgreso">＋ Registrar Egreso</button>
</div>

{{-- Tabla --}}
<div class="data-table-wrapper" id="egresosTableWrapper">
    <table class="data-table" id="egresosTable">
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
        <tbody id="egresosTbody"></tbody>
    </table>
</div>

{{-- Empty state --}}
<div class="table-empty" id="egresosEmpty" style="display:none">
    <div class="empty-state" style="min-height:300px">
        <div class="empty-state-illustration" style="border-color:rgba(239,68,68,0.25)">
            <div class="empty-state-pulse" style="border-color:rgba(239,68,68,0.2)"></div>
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polyline points="8,16 18,28 28,22 40,38 52,34" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7" fill="none"/>
                <circle cx="40" cy="38" r="4" fill="#ef4444" opacity="0.8"/>
                <line x1="40" y1="38" x2="40" y2="46" stroke="#ef4444" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="36" y1="42" x2="40" y2="46" stroke="#ef4444" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="44" y1="42" x2="40" y2="46" stroke="#ef4444" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                <line x1="8" y1="48" x2="52" y2="48" stroke="#ef4444" stroke-width="1" opacity="0.3"/>
            </svg>
        </div>
        <h2 class="empty-state-title">Sin egresos registrados</h2>
        <p class="empty-state-desc">
            No se registraron gastos ni egresos todavía.<br>
            Llevá el control de los gastos del kiosko desde aquí.
        </p>
        <button class="btn btn-gen btn-egreso-add" id="btnNewEgresoEmpty">＋ Registrar egreso</button>
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="egresosToast"></div>

{{-- ══════════════════ MODAL: CREAR / EDITAR EGRESO ══════════════════ --}}
<div class="modal-overlay" id="egresoFormModal">
    <div class="modal" style="max-width:560px">
        <div class="modal-header" style="border-bottom-color:rgba(239,68,68,0.2)">
            <h3 class="modal-title" id="egresoModalTitle">📉 Nuevo Egreso</h3>
            <button class="modal-close" id="egresoModalClose">✕</button>
        </div>
        <form class="modal-body" id="egresoForm">
            <input type="hidden" id="egreso-edit-id">

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Fecha <span style="color:#ef4444">*</span></label>
                    <input class="form-input" type="date" name="fecha" id="egreso-fecha" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Categoría <span style="color:#ef4444">*</span></label>
                    <select class="form-input" name="tipo" id="egreso-tipo" required>
                        <option value="">Seleccionar...</option>
                        <option value="gasto_operativo">Gasto operativo</option>
                        <option value="pasivo">Pasivo / Deuda</option>
                        <option value="insumos">Insumos</option>
                        <option value="servicio">Servicios</option>
                        <option value="impuesto">Impuesto / Tasa</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Descripción <span style="color:#ef4444">*</span></label>
                <input class="form-input" type="text" name="descripcion" id="egreso-descripcion"
                       placeholder="Ej: Pago de servicio eléctrico..." required maxlength="200">
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Monto <span style="color:#ef4444">*</span></label>
                    <input class="form-input" type="number" name="monto" id="egreso-monto"
                           step="0.01" min="0.01" placeholder="$0,00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Estado <span style="color:#ef4444">*</span></label>
                    <select class="form-input" name="estado" id="egreso-estado" required>
                        <option value="efectuado">✅ Efectuado</option>
                        <option value="pendiente">🕐 Pendiente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Detalle / Observaciones</label>
                <textarea class="form-input" name="detalle" id="egreso-detalle"
                          rows="3" placeholder="Información adicional sobre este egreso..."
                          style="resize:vertical; min-height:72px"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="egresoCancelBtn">Cancelar</button>
                <button type="submit" class="btn-submit btn-submit-red" id="egresoSubmitBtn">Registrar Egreso</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MODAL: ELIMINAR EGRESO ══════════════════ --}}
<div class="modal-overlay" id="egresoDeleteModal">
    <div class="modal" style="max-width:440px">
        <div class="modal-header">
            <h3 class="modal-title">⚠️ Confirmar Eliminación</h3>
            <button class="modal-close" id="egresoDelClose">✕</button>
        </div>
        <form class="modal-body" id="egresoDeleteForm">
            <input type="hidden" id="egreso-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">
                ¿Estás seguro de que querés eliminar este egreso?
            </p>
            <div class="mov-del-preview" id="egresoDelPreview"></div>
            <p style="color:var(--text-muted);font-size:.78rem;margin-top:12px">Esta acción no se puede deshacer.</p>
            <div class="modal-footer" style="margin-top:16px">
                <button type="button" class="btn-cancel" id="egresoDelCancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    /* ── Estado ── */
    let egresosData = [];
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
        gasto_operativo: 'Gasto operativo',
        pasivo:          'Pasivo / Deuda',
        insumos:         'Insumos',
        servicio:        'Servicios',
        impuesto:        'Impuesto / Tasa',
        otro:            'Otro',
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
        const t = document.getElementById('egresosToast');
        if (!t) return;
        t.textContent = msg;
        t.className = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ── Render tabla ── */
    function filtered() {
        return egresosData.filter(r => {
            if (filterEstado && r.estado !== filterEstado) return false;
            if (filterTipo   && r.tipo   !== filterTipo)   return false;
            return true;
        });
    }

    function renderTable() {
        const tbody   = document.getElementById('egresosTbody');
        const wrapper = document.getElementById('egresosTableWrapper');
        const empty   = document.getElementById('egresosEmpty');
        const countBadge = document.getElementById('egresosTotalCount');
        const montoBadge = document.getElementById('egresosTotalMonto');
        const pendBadge  = document.getElementById('egresosPendienteCount');
        if (!tbody) return;

        const all  = egresosData;
        const rows = filtered();

        const totalMonto = all.reduce((s, r) => s + parseFloat(r.monto || 0), 0);
        const pendientes = all.filter(r => r.estado === 'pendiente').length;

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
                    <span class="mov-tipo-badge mov-tipo-egreso">${tipoLabel(r.tipo)}</span>
                </td>
                <td style="font-weight:700;color:#ef4444;white-space:nowrap">−${formatMoney(r.monto)}</td>
                <td>
                    <span class="mov-estado-badge ${r.estado === 'efectuado' ? 'estado-efectuado' : 'estado-pendiente'}">
                        ${r.estado === 'efectuado' ? '✅ Efectuado' : '🕐 Pendiente'}
                    </span>
                </td>
                <td>
                    <div class="actions-cell" style="justify-content:center">
                        <button class="action-btn" title="Editar" onclick="EGRESOS.edit(${r.id})">✏️</button>
                        <button class="action-btn danger" title="Eliminar" onclick="EGRESOS.del(${r.id})">🗑️</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /* ── Cargar ── */
    async function load() {
        if (loaded) return;
        try {
            const data = await api('/panel/api/egresos');
            egresosData = data.egresos || [];
            loaded = true;
            renderTable();
        } catch (e) {
            toast('Error al cargar egresos desde el servidor', 'error');
        }
    }

    /* Observer de activación */
    const section = document.querySelector('[data-module-content="egresos"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) load();
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
    }

    /* ── Filtros ── */
    document.getElementById('egresosFilterEstado')?.addEventListener('change', function() {
        filterEstado = this.value;
        renderTable();
    });
    document.getElementById('egresosFilterTipo')?.addEventListener('change', function() {
        filterTipo = this.value;
        renderTable();
    });

    /* ── Abrir modal nuevo ── */
    function openNewModal() {
        document.getElementById('egresoForm')?.reset();
        document.getElementById('egreso-edit-id').value = '';
        document.getElementById('egresoModalTitle').textContent = '📉 Nuevo Egreso';
        document.getElementById('egresoSubmitBtn').textContent  = 'Registrar Egreso';
        const today = new Date().toISOString().slice(0, 10);
        document.getElementById('egreso-fecha').value = today;
        window.openModal('egresoFormModal');
    }

    document.getElementById('btnNewEgreso')?.addEventListener('click', openNewModal);
    document.getElementById('btnNewEgresoEmpty')?.addEventListener('click', openNewModal);

    /* ── Editar ── */
    window.EGRESOS = window.EGRESOS || {};

    EGRESOS.edit = function(id) {
        const r = egresosData.find(x => x.id === id);
        if (!r) return;
        document.getElementById('egreso-edit-id').value = id;
        document.getElementById('egreso-fecha').value       = r.fecha;
        document.getElementById('egreso-tipo').value        = r.tipo;
        document.getElementById('egreso-descripcion').value = r.descripcion;
        document.getElementById('egreso-monto').value       = r.monto;
        document.getElementById('egreso-estado').value      = r.estado;
        document.getElementById('egreso-detalle').value     = r.detalle || '';
        document.getElementById('egresoModalTitle').textContent = '✏️ Editar Egreso';
        document.getElementById('egresoSubmitBtn').textContent  = 'Guardar Cambios';
        window.openModal('egresoFormModal');
    };

    /* ── Eliminar ── */
    EGRESOS.del = function(id) {
        const r = egresosData.find(x => x.id === id);
        if (!r) return;
        document.getElementById('egreso-del-id').value = id;
        document.getElementById('egresoDelPreview').innerHTML = `
            <div class="mov-del-detail">
                <span class="mov-del-label">Descripción</span>
                <span>${r.descripcion}</span>
            </div>
            <div class="mov-del-detail">
                <span class="mov-del-label">Monto</span>
                <span style="color:#ef4444;font-weight:700">−${formatMoney(r.monto)}</span>
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
        window.openModal('egresoDeleteModal');
    };

    /* ── Submit form ── */
    document.getElementById('egresoForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd  = new FormData(e.target);
        const id  = document.getElementById('egreso-edit-id').value;
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
                await api(`/panel/api/egresos/${id}`, 'PUT', rec);
                toast('Egreso actualizado correctamente');
            } else {
                await api('/panel/api/egresos', 'POST', rec);
                toast('Egreso registrado exitosamente');
            }
            loaded = false;
            await load();
            window.closeModal('egresoFormModal');
        } catch (err) {
            toast(err.message || 'Error al procesar el egreso', 'error');
        }
    });

    /* ── Delete form ── */
    document.getElementById('egresoDeleteForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('egreso-del-id').value;
        try {
            await api(`/panel/api/egresos/${id}`, 'DELETE');
            toast('Egreso eliminado');
            loaded = false;
            await load();
            window.closeModal('egresoDeleteModal');
        } catch (err) {
            toast('Error al eliminar el egreso', 'error');
        }
    });

})();
</script>
