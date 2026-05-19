// ============================================================
// KIOSKO-UNER | SUPER ADMIN PANEL – JS
// ============================================================
(function () {
    'use strict';

    // ── Elements ──────────────────────────────────────────────
    const sidebar      = document.getElementById('sidebar');
    const overlay      = document.getElementById('sidebarOverlay');
    const btnCollapse  = document.getElementById('btnCollapse');
    const btnHamburger = document.getElementById('btnHamburger');
    const navItems     = document.querySelectorAll('.nav-item[data-module]');
    const modules      = document.querySelectorAll('.module-section');
    const headerTitle  = document.getElementById('headerTitle');
    const headerBadge  = document.getElementById('headerBadge');
    const csrfToken    = document.querySelector('meta[name="csrf-token"]')?.content;

    // ── Sidebar collapse/expand ───────────────────────────────
    if (btnCollapse) {
        btnCollapse.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sa-sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
    }
    if (localStorage.getItem('sa-sidebar-collapsed') === 'true') sidebar.classList.add('collapsed');

    // ── Mobile sidebar ────────────────────────────────────────
    function openSidebar() { sidebar.classList.add('mobile-open'); overlay.classList.add('visible'); document.body.style.overflow = 'hidden'; }
    function closeSidebar() { sidebar.classList.remove('mobile-open'); overlay.classList.remove('visible'); document.body.style.overflow = ''; }
    if (btnHamburger) btnHamburger.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });

    // ── Module navigation ─────────────────────────────────────
    function activateModule(id) {
        navItems.forEach(i => i.classList.toggle('active', i.dataset.module === id));
        modules.forEach(m => m.classList.toggle('active', m.dataset.moduleContent === id));
        const nav = document.querySelector(`.nav-item[data-module="${id}"]`);
        if (nav && headerTitle) headerTitle.textContent = nav.dataset.title || id;
        if (nav && headerBadge) headerBadge.textContent = nav.dataset.badge || id;
        localStorage.setItem('sa-active-module', id);
        if (window.innerWidth <= 768) closeSidebar();
        if (id === 'personal') loadPersonnel();
        if (id === 'proveedores') loadProveedores();
    }
    navItems.forEach(i => i.addEventListener('click', () => activateModule(i.dataset.module)));
    const saved = localStorage.getItem('sa-active-module');
    activateModule(saved || (navItems.length ? navItems[0].dataset.module : 'inicio'));

    // ── Data state ────────────────────────────────────────────
    let personnelData = [];

    // ── Toast ─────────────────────────────────────────────────
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3000);
    }

    // ── API helper ────────────────────────────────────────────
    async function api(url, method = 'GET', body = null) {
        const opts = { method, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } };
        if (body) { opts.headers['Content-Type'] = 'application/json'; opts.body = JSON.stringify(body); }
        const res = await fetch(url, opts);
        const data = await res.json();
        if (!res.ok) throw data;
        return data;
    }

    // ── Load Personnel ────────────────────────────────────────
    async function loadPersonnel() {
        try {
            const data = await api('/superadmin/personnel');
            personnelData = data.personnel;
            renderStats(data.counts);
            renderTable(personnelData);
        } catch (e) { showToast('Error al cargar datos', 'error'); }
    }

    function renderStats(c) {
        const el = (id) => document.getElementById(id);
        if (el('stat-total')) el('stat-total').textContent = c.total;
        if (el('stat-alumnos')) el('stat-alumnos').textContent = c.alumnos;
        if (el('stat-profesores')) el('stat-profesores').textContent = c.profesores;
        if (el('stat-directivos')) el('stat-directivos').textContent = c.directivos;
    }

    function renderTable(data) {
        const tbody = document.getElementById('personnelBody');
        const empty = document.getElementById('tableEmpty');
        if (!tbody) return;
        if (!data.length) {
            tbody.innerHTML = '';
            if (empty) empty.style.display = 'block';
            return;
        }
        if (empty) empty.style.display = 'none';
        tbody.innerHTML = data.map(p => `
            <tr data-id="${p.id}">
                <td>${p.dni}</td>
                <td>${p.apellido}, ${p.nombre}</td>
                <td>${p.telefono || '—'}</td>
                <td>${p.correo}</td>
                <td><span class="role-badge ${p.role}">${p.role}</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar" onclick="SA.editPersonnel(${p.id})">✏️</button>
                    <button class="action-btn" title="Cambiar rol" onclick="SA.changeRole(${p.id})">🔑</button>
                    <button class="action-btn" title="Cambiar contraseña" onclick="SA.changePassword(${p.id})">🔒</button>
                    <button class="action-btn danger" title="Eliminar" onclick="SA.deletePersonnel(${p.id})">🗑️</button>
                </td>
            </tr>
        `).join('');
    }

    // ── Search & Filter ───────────────────────────────────────
    const searchInput = document.getElementById('searchInput');
    const filterRole  = document.getElementById('filterRole');
    function applyFilters() {
        const q = (searchInput?.value || '').toLowerCase();
        const r = filterRole?.value || '';
        const filtered = personnelData.filter(p => {
            const matchQ = !q || p.nombre.toLowerCase().includes(q) || p.apellido.toLowerCase().includes(q) || p.dni.includes(q) || p.correo.toLowerCase().includes(q);
            const matchR = !r || p.role === r;
            return matchQ && matchR;
        });
        renderTable(filtered);
    }
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterRole) filterRole.addEventListener('change', applyFilters);

    // ── Modal helpers ─────────────────────────────────────────
    function openModal(id) { document.getElementById(id)?.classList.add('visible'); }
    function closeModal(id) { document.getElementById(id)?.classList.remove('visible'); }
    function resetForm(id) { document.getElementById(id)?.reset(); }

    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) o.classList.remove('visible'); });
    });
    document.querySelectorAll('.modal-close, .btn-cancel').forEach(b => {
        b.addEventListener('click', () => b.closest('.modal-overlay')?.classList.remove('visible'));
    });

    // ── CREATE ────────────────────────────────────────────────
    document.getElementById('btnNewPersonnel')?.addEventListener('click', () => {
        resetForm('createForm');
        document.getElementById('createFormTitle').textContent = 'Nuevo Personal';
        openModal('createModal');
    });

    document.getElementById('createForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const body = Object.fromEntries(fd.entries());
        try {
            const data = await api('/superadmin/personnel', 'POST', body);
            showToast(data.message);
            closeModal('createModal');
            loadPersonnel();
        } catch (err) {
            const msg = err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al crear';
            showToast(msg, 'error');
        }
    });

    // ── EDIT ──────────────────────────────────────────────────
    window.SA = window.SA || {};

    SA.editPersonnel = function (id) {
        const p = personnelData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('edit-id').value = p.id;
        document.getElementById('edit-dni').value = p.dni;
        document.getElementById('edit-nombre').value = p.nombre;
        document.getElementById('edit-apellido').value = p.apellido;
        document.getElementById('edit-telefono').value = p.telefono || '';
        document.getElementById('edit-correo').value = p.correo;
        openModal('editModal');
    };

    document.getElementById('editForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const id = fd.get('id');
        const body = Object.fromEntries(fd.entries());
        delete body.id;
        try {
            const data = await api(`/superadmin/personnel/${id}`, 'PUT', body);
            showToast(data.message);
            closeModal('editModal');
            loadPersonnel();
        } catch (err) {
            const msg = err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al actualizar';
            showToast(msg, 'error');
        }
    });

    // ── CHANGE ROLE ───────────────────────────────────────────
    SA.changeRole = function (id) {
        const p = personnelData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('role-id').value = p.id;
        document.getElementById('role-name').textContent = `${p.apellido}, ${p.nombre}`;
        document.getElementById('role-select').value = p.role;
        openModal('roleModal');
    };

    document.getElementById('roleForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const id = fd.get('id');
        try {
            const data = await api(`/superadmin/personnel/${id}/role`, 'PUT', { role: fd.get('role') });
            showToast(data.message);
            closeModal('roleModal');
            loadPersonnel();
        } catch (err) { showToast(err.message || 'Error', 'error'); }
    });

    // ── CHANGE PASSWORD ───────────────────────────────────────
    SA.changePassword = function (id) {
        const p = personnelData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('pw-id').value = p.id;
        document.getElementById('pw-name').textContent = `${p.apellido}, ${p.nombre}`;
        document.getElementById('pw-input').value = '';
        openModal('passwordModal');
    };

    document.getElementById('passwordForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const id = fd.get('id');
        try {
            const data = await api(`/superadmin/personnel/${id}/password`, 'PUT', { password: fd.get('password') });
            showToast(data.message);
            closeModal('passwordModal');
        } catch (err) { showToast(err.message || 'Error', 'error'); }
    });

    // ── DELETE ─────────────────────────────────────────────────
    SA.deletePersonnel = function (id) {
        const p = personnelData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('del-id').value = p.id;
        document.getElementById('del-name').textContent = `${p.apellido}, ${p.nombre}`;
        openModal('deleteModal');
    };

    document.getElementById('deleteForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = new FormData(e.target).get('id');
        try {
            const data = await api(`/superadmin/personnel/${id}`, 'DELETE');
            showToast(data.message);
            closeModal('deleteModal');
            loadPersonnel();
        } catch (err) { showToast(err.message || 'Error', 'error'); }
    });

    // ── Initial load ──────────────────────────────────────────
    loadPersonnel();

    // ══════════════════════════════════════════════════════════
    // PROVEEDORES
    // ══════════════════════════════════════════════════════════

    let proveedoresData = [];

    // Load
    async function loadProveedores() {
        try {
            const data = await api('/superadmin/proveedores');
            proveedoresData = data.proveedores;
            renderProveedoresTable(proveedoresData);
        } catch (e) { showToast('Error al cargar proveedores', 'error'); }
    }

    // Render table
    function renderProveedoresTable(data) {
        const tbody = document.getElementById('proveedoresBody');
        const empty = document.getElementById('provTableEmpty');
        if (!tbody) return;
        if (!data.length) {
            tbody.innerHTML = '';
            if (empty) empty.style.display = 'block';
            return;
        }
        if (empty) empty.style.display = 'none';
        tbody.innerHTML = data.map(p => `
            <tr data-id="${p.id}">
                <td><strong>${p.nombre}</strong></td>
                <td>${p.contacto || '—'}</td>
                <td>${p.telefono || '—'}</td>
                <td>${p.correo || '—'}</td>
                <td>${p.direccion || '—'}</td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar" onclick="SA.editProveedor(${p.id})">✏️</button>
                    <button class="action-btn danger" title="Eliminar" onclick="SA.deleteProveedor(${p.id})">🗑️</button>
                </td>
            </tr>
        `).join('');
    }

    // Search
    const provSearchInput = document.getElementById('provSearchInput');
    function applyProvFilters() {
        const q = (provSearchInput?.value || '').toLowerCase();
        const filtered = proveedoresData.filter(p =>
            !q ||
            p.nombre.toLowerCase().includes(q) ||
            (p.contacto || '').toLowerCase().includes(q) ||
            (p.correo || '').toLowerCase().includes(q)
        );
        renderProveedoresTable(filtered);
    }
    if (provSearchInput) provSearchInput.addEventListener('input', applyProvFilters);

    // ── CREATE
    document.getElementById('btnNewProveedor')?.addEventListener('click', () => {
        document.getElementById('provCreateForm')?.reset();
        openModal('provCreateModal');
    });

    document.getElementById('provCreateForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const body = Object.fromEntries(new FormData(e.target).entries());
        try {
            const data = await api('/superadmin/proveedores', 'POST', body);
            showToast(data.message);
            closeModal('provCreateModal');
            loadProveedores();
        } catch (err) {
            const msg = err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al crear';
            showToast(msg, 'error');
        }
    });

    // ── EDIT
    SA.editProveedor = function (id) {
        const p = proveedoresData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('prov-edit-id').value       = p.id;
        document.getElementById('prov-edit-nombre').value   = p.nombre;
        document.getElementById('prov-edit-contacto').value = p.contacto || '';
        document.getElementById('prov-edit-telefono').value = p.telefono || '';
        document.getElementById('prov-edit-correo').value   = p.correo || '';
        document.getElementById('prov-edit-direccion').value = p.direccion || '';
        openModal('provEditModal');
    };

    document.getElementById('provEditForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const id = fd.get('id');
        const body = Object.fromEntries(fd.entries());
        delete body.id;
        try {
            const data = await api(`/superadmin/proveedores/${id}`, 'PUT', body);
            showToast(data.message);
            closeModal('provEditModal');
            loadProveedores();
        } catch (err) {
            const msg = err.message || Object.values(err.errors || {}).flat().join(', ') || 'Error al actualizar';
            showToast(msg, 'error');
        }
    });

    // ── DELETE
    SA.deleteProveedor = function (id) {
        const p = proveedoresData.find(x => x.id === id);
        if (!p) return;
        document.getElementById('prov-del-id').value = p.id;
        document.getElementById('prov-del-name').textContent = p.nombre;
        openModal('provDeleteModal');
    };

    document.getElementById('provDeleteForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = new FormData(e.target).get('id');
        try {
            const data = await api(`/superadmin/proveedores/${id}`, 'DELETE');
            showToast(data.message);
            closeModal('provDeleteModal');
            loadProveedores();
        } catch (err) { showToast(err.message || 'Error', 'error'); }
    });

})();
