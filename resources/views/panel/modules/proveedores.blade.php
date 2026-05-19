{{-- MÓDULO: PROVEEDORES – Panel Alumno (CRUD completo) --}}

{{-- Toolbar --}}
<div class="table-toolbar">
    <input class="search-input" type="text" id="provSearchInput" placeholder="🔍  Buscar por nombre, contacto o correo...">
    <button class="btn btn-gen" id="btnNewProveedor">＋ Nuevo Proveedor</button>
</div>

{{-- Tabla --}}
<div class="data-table-wrapper">
    <table class="data-table" id="proveedoresTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="proveedoresBody"></tbody>
    </table>
    <div class="table-empty" id="provTableEmpty">
        No hay proveedores registrados. Hacé clic en "＋ Nuevo Proveedor" para comenzar.
    </div>
</div>

{{-- Toast --}}
<div class="prov-toast" id="provToast"></div>

{{-- ══════════════════ MODALES ══════════════════ --}}

{{-- Modal: Crear --}}
<div class="modal-overlay" id="provCreateModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nuevo Proveedor</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provCreateForm">
            <div class="form-group">
                <label class="form-label">Nombre del proveedor <span style="color:#ef4444">*</span></label>
                <input class="form-input" name="nombre" required placeholder="Ej: Distribuidora ABC">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Persona de contacto</label>
                    <input class="form-input" name="contacto" placeholder="Ej: Juan Pérez">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" placeholder="Ej: 3434-000000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" placeholder="proveedor@ejemplo.com">
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input class="form-input" name="direccion" placeholder="Ej: Av. Ramírez 123, Concordia">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Crear Proveedor</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Editar --}}
<div class="modal-overlay" id="provEditModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Editar Proveedor</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provEditForm">
            <input type="hidden" name="id" id="prov-edit-id">
            <div class="form-group">
                <label class="form-label">Nombre del proveedor <span style="color:#ef4444">*</span></label>
                <input class="form-input" name="nombre" id="prov-edit-nombre" required>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Persona de contacto</label>
                    <input class="form-input" name="contacto" id="prov-edit-contacto">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input class="form-input" name="telefono" id="prov-edit-telefono">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-input" type="email" name="correo" id="prov-edit-correo">
            </div>
            <div class="form-group">
                <label class="form-label">Dirección</label>
                <input class="form-input" name="direccion" id="prov-edit-direccion">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Eliminar --}}
<div class="modal-overlay" id="provDeleteModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Eliminación</h3>
            <button class="modal-close">✕</button>
        </div>
        <form class="modal-body" id="provDeleteForm">
            <input type="hidden" name="id" id="prov-del-id">
            <p style="color:var(--text-secondary);font-size:.88rem;margin-bottom:8px">
                ¿Estás seguro de que querés eliminar al proveedor:
            </p>
            <p style="color:#ef4444;font-weight:700;font-size:1rem;margin-bottom:16px" id="prov-del-name"></p>
            <p style="color:var(--text-muted);font-size:.8rem;margin-bottom:16px">
                Esta acción no se puede deshacer.
            </p>
            <div class="modal-footer">
                <button type="button" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ SCRIPT ══════════════════ --}}
<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let proveedoresData = [];
    let loaded = false;

    /* ── API helper ── */
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
        const t = document.getElementById('provToast');
        if (!t) return;
        t.textContent = msg;
        t.className = `prov-toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3200);
    }

    /* ── Modal helpers ── */
    function openModal(id)  { document.getElementById(id)?.classList.add('visible'); }
    function closeModal(id) { document.getElementById(id)?.classList.remove('visible'); }

    document.querySelectorAll('#provCreateModal .modal-close, #provCreateModal .btn-cancel').forEach(b =>
        b.addEventListener('click', () => closeModal('provCreateModal')));
    document.querySelectorAll('#provEditModal .modal-close, #provEditModal .btn-cancel').forEach(b =>
        b.addEventListener('click', () => closeModal('provEditModal')));
    document.querySelectorAll('#provDeleteModal .modal-close, #provDeleteModal .btn-cancel').forEach(b =>
        b.addEventListener('click', () => closeModal('provDeleteModal')));

    ['provCreateModal','provEditModal','provDeleteModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', e => {
            if (e.target === e.currentTarget) closeModal(id);
        });
    });

    /* ── Render & Ordenar ── */
    function sortData() {
        proveedoresData.sort((a, b) => {
            const aDel = !!a.deleted_at;
            const bDel = !!b.deleted_at;
            if (aDel && !bDel) return 1;
            if (!aDel && bDel) return -1;
            return (a.nombre || '').localeCompare(b.nombre || '');
        });
    }

    function render(data) {
        const tbody = document.getElementById('proveedoresBody');
        const empty = document.getElementById('provTableEmpty');
        if (!tbody) return;
        if (!data.length) {
            tbody.innerHTML = '';
            if (empty) empty.style.display = 'block';
            return;
        }
        if (empty) empty.style.display = 'none';
        tbody.innerHTML = data.map(p => {
            // Un proveedor está eliminado solo si deleted_at existe y tiene un valor
            const isDeleted = !!p.deleted_at;
            const rowClass = isDeleted ? 'row-deleted' : '';
            const actions = isDeleted 
                ? `<span style="font-size:0.75rem; color:var(--text-muted); font-style:italic">Eliminado</span>`
                : `<button class="action-btn"        title="Editar"    onclick="PROV.edit(${p.id})">✏️</button>
                   <button class="action-btn danger" title="Eliminar"  onclick="PROV.del(${p.id})">🗑️</button>`;
                   
            return `
            <tr data-id="${p.id}" class="${rowClass}">
                <td><strong>${p.nombre}</strong></td>
                <td>${p.contacto  || '—'}</td>
                <td>${p.telefono  || '—'}</td>
                <td>${p.correo    || '—'}</td>
                <td>${p.direccion || '—'}</td>
                <td class="actions-cell">
                    ${actions}
                </td>
            </tr>
            `;
        }).join('');
    }

    /* ── Load ── */
    async function load() {
        if (loaded) return;
        try {
            const data = await api('/superadmin/proveedores');
            proveedoresData = data.proveedores || [];
            sortData();
            loaded = true;
            render(proveedoresData);
        } catch (e) { toast('Error al cargar proveedores', 'error'); }
    }

    /* Cargar cuando el módulo se activa */
    const section = document.querySelector('[data-module-content="proveedores"]');
    if (section) {
        new MutationObserver(() => {
            if (section.classList.contains('active')) load();
        }).observe(section, { attributes: true, attributeFilter: ['class'] });
    }

    /* ── Búsqueda ── */
    document.getElementById('provSearchInput')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        // Al buscar, conservamos el orden existente porque ya está ordenado
        render(proveedoresData.filter(p =>
            !q ||
            p.nombre.toLowerCase().includes(q) ||
            (p.contacto  || '').toLowerCase().includes(q) ||
            (p.correo    || '').toLowerCase().includes(q)
        ));
    });

    /* ── CREATE ── */
    document.getElementById('btnNewProveedor')?.addEventListener('click', () => {
        document.getElementById('provCreateForm')?.reset();
        openModal('provCreateModal');
    });

    document.getElementById('provCreateForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const body = Object.fromEntries(new FormData(e.target).entries());
        try {
            const data = await api('/superadmin/proveedores', 'POST', body);
            toast(data.message);
            closeModal('provCreateModal');
            proveedoresData.push(data.proveedor);
            sortData();
            loaded = true;
            render(proveedoresData);
        } catch (err) {
            toast(err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al crear', 'error');
        }
    });

    /* ── EDIT ── */
    window.PROV = window.PROV || {};
    PROV.edit = function (id) {
        const p = proveedoresData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('prov-edit-id').value        = p.id;
        document.getElementById('prov-edit-nombre').value    = p.nombre;
        document.getElementById('prov-edit-contacto').value  = p.contacto  || '';
        document.getElementById('prov-edit-telefono').value  = p.telefono  || '';
        document.getElementById('prov-edit-correo').value    = p.correo    || '';
        document.getElementById('prov-edit-direccion').value = p.direccion || '';
        openModal('provEditModal');
    };

    document.getElementById('provEditForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const id = fd.get('id');
        const body = Object.fromEntries(fd.entries());
        delete body.id;
        try {
            const data = await api(`/superadmin/proveedores/${id}`, 'PUT', body);
            toast(data.message);
            closeModal('provEditModal');
            const idx = proveedoresData.findIndex(x => x.id == id);
            if (idx !== -1) proveedoresData[idx] = { ...proveedoresData[idx], ...body };
            sortData();
            render(proveedoresData);
        } catch (err) {
            toast(err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al actualizar', 'error');
        }
    });

    /* ── DELETE ── */
    PROV.del = function (id) {
        const p = proveedoresData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('prov-del-id').value = p.id;
        document.getElementById('prov-del-name').textContent = p.nombre;
        openModal('provDeleteModal');
    };

    document.getElementById('provDeleteForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        const id = new FormData(e.target).get('id');
        try {
            const data = await api(`/superadmin/proveedores/${id}`, 'DELETE');
            toast(data.message);
            closeModal('provDeleteModal');
            
            // Marcar como borrado
            const idx = proveedoresData.findIndex(x => x.id == id);
            if (idx !== -1) {
                proveedoresData[idx].deleted_at = new Date().toISOString();
            }
            
            sortData();
            render(proveedoresData);
        } catch (err) { toast(err.message || 'Error al eliminar', 'error'); }
    });
})();
</script>
