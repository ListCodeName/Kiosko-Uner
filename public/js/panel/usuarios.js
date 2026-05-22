/**
 * AlumnoUserModule
 * Gestión de compañeros alumnos en el Panel del Alumno.
 * Única acción disponible: cambiar la contraseña de otro alumno.
 */

const AlumnoUserModule = (function () {

    let users = [];
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Referencias DOM ──────────────────────────────────────────
    function els() {
        return {
            tableBody:    document.getElementById('usrTableBody'),
            searchInput:  document.getElementById('usrSearch'),

            pwdModal:     document.getElementById('usrPasswordModal'),
            pwdForm:      document.getElementById('usrPasswordForm'),
            pwdTargetId:  document.getElementById('usrPwdTargetId'),
            pwdTargetName:document.getElementById('usrPwdTargetName'),
            pwdInput:     document.getElementById('usrPwdInput'),
            pwdClose:     document.getElementById('usrPwdClose'),
            pwdCancel:    document.getElementById('usrPwdCancel'),
        };
    }

    // ── Toast ────────────────────────────────────────────────────
    function toast(msg, error = false) {
        if (window.showToast) window.showToast(msg, error ? 'error' : 'success');
        else alert(msg);
    }

    // ── Normalización para búsqueda accent-insensitive ───────────
    function normalize(s) {
        return (s ?? '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    // ── Carga de usuarios desde el servidor ──────────────────────
    async function loadUsers() {
        const e = els();
        if (!e.tableBody) return;

        e.tableBody.innerHTML = '<tr><td colspan="5" class="usr-empty">Cargando compañeros…</td></tr>';

        try {
            const res  = await fetch('/panel/api/users');
            const data = await res.json();

            if (res.ok) {
                users = data.users ?? [];
                renderTable();
            } else {
                toast(data.message || 'Error al cargar la lista', true);
                e.tableBody.innerHTML = '<tr><td colspan="5" class="usr-empty">Error al cargar los datos.</td></tr>';
            }
        } catch (err) {
            console.error('[AlumnoUserModule] loadUsers:', err);
            toast('Error de conexión', true);
        }
    }

    // ── Renderizado de la tabla ──────────────────────────────────
    function renderTable() {
        const e    = els();
        if (!e.tableBody) return;

        const q    = normalize(e.searchInput?.value ?? '');

        const filtered = users.filter(u => {
            return normalize(u.nombre).includes(q)   ||
                   normalize(u.apellido).includes(q) ||
                   normalize(u.dni ?? '').includes(q);
        });

        if (filtered.length === 0) {
            e.tableBody.innerHTML = '<tr><td colspan="5" class="usr-empty">No se encontraron compañeros.</td></tr>';
            return;
        }

        e.tableBody.innerHTML = filtered.map(u => `
            <tr>
                <td>${u.dni ?? '-'}</td>
                <td>${u.apellido ?? ''}, ${u.nombre ?? ''}</td>
                <td style="color:var(--text-secondary);font-size:.82rem">${u.username ?? '-'}</td>
                <td><span class="usr-badge">alumno</span></td>
                <td>
                    <button
                        class="usr-action-btn"
                        title="Cambiar contraseña"
                        onclick="AlumnoUserModule.openPasswordModal(${u.id}, '${escHtml(u.apellido)}, ${escHtml(u.nombre)}')"
                    >🔒 Contraseña</button>
                </td>
            </tr>
        `).join('');
    }

    // ── Escape básico para uso inline en onclick ─────────────────
    function escHtml(str) {
        return (str ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    // ── Modal: Cambiar Contraseña ────────────────────────────────
    function openPasswordModal(id, fullName) {
        const e = els();
        if (!e.pwdModal) return;

        e.pwdForm.reset();
        e.pwdTargetId.value   = id;
        e.pwdTargetName.textContent = fullName;

        e.pwdModal.classList.add('visible');
    }

    function closePasswordModal() {
        const e = els();
        e.pwdModal?.classList.remove('visible');
        e.pwdForm?.reset();
    }

    async function savePassword(event) {
        event.preventDefault();
        const e  = els();
        const id = e.pwdTargetId.value;
        const pw = e.pwdInput.value;

        try {
            const res  = await fetch(`/panel/api/users/${id}/password`, {
                method:  'PUT',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  csrf(),
                },
                body: JSON.stringify({ password: pw }),
            });
            const data = await res.json();

            if (res.ok) {
                toast(data.message ?? 'Contraseña actualizada correctamente.');
                closePasswordModal();
            } else {
                toast(data.message || 'Error al actualizar la contraseña', true);
            }
        } catch (err) {
            console.error('[AlumnoUserModule] savePassword:', err);
            toast('Error de conexión', true);
        }
    }

    // ── Inicialización ───────────────────────────────────────────
    function init() {
        const e = els();
        if (!e.tableBody) return; // Módulo no activo en esta página

        // Listeners de búsqueda
        e.searchInput?.addEventListener('input', renderTable);

        // Listeners del modal
        e.pwdForm?.addEventListener('submit', savePassword);
        e.pwdClose?.addEventListener('click', closePasswordModal);
        e.pwdCancel?.addEventListener('click', closePasswordModal);

        // Cerrar modal al hacer click fuera
        e.pwdModal?.addEventListener('click', function (ev) {
            if (ev.target === e.pwdModal) closePasswordModal();
        });

        loadUsers();
    }

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ── API pública ──────────────────────────────────────────────
    return {
        loadUsers,
        openPasswordModal,
    };

})();
