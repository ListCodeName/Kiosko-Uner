/**
 * Módulo de Gestión de Usuarios para el Panel del Profesor
 */

const UserModule = (function () {
    let users = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Elementos del DOM
    const els = {
        tableBody: document.getElementById('userTableBody'),
        searchInput: document.getElementById('userSearch'),
        filterSelect: document.getElementById('userFilterRole'),
        
        createModal: document.getElementById('userCreateModal'),
        modalTitle: document.getElementById('userModalTitle'),
        form: document.getElementById('userForm'),
        id: document.getElementById('userId'),
        nombre: document.getElementById('userNombre'),
        apellido: document.getElementById('userApellido'),
        dni: document.getElementById('userDni'),
        telefono: document.getElementById('userTelefono'),
        correo: document.getElementById('userCorreo'),
        username: document.getElementById('userUsername'),
        password: document.getElementById('userPassword'),
        passwordHelp: document.getElementById('userPasswordHelp'),
        role: document.getElementById('userRole'),

        passwordModal: document.getElementById('userPasswordModal'),
        pwdForm: document.getElementById('userPasswordForm'),
        pwdId: document.getElementById('pwdUserId'),
        newPassword: document.getElementById('newPassword'),
    };

    /**
     * Mostrar toast (notificación)
     */
    function showToast(message, isError = false) {
        if (window.showToast) {
            window.showToast(message, isError ? 'error' : 'success');
        } else {
            alert(message);
        }
    }

    /**
     * Obtener los usuarios de la base de datos
     */
    async function loadUsers() {
        try {
            els.tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando usuarios...</td></tr>';
            const response = await fetch('/profesor/api/users');
            const data = await response.json();
            
            if (response.ok) {
                users = data.users || [];
                renderTable();
            } else {
                showToast(data.message || 'Error al cargar usuarios', true);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', true);
        }
    }

    /**
     * Renderizar la tabla de usuarios
     */
    function renderTable() {
        const searchTerm = els.searchInput.value.toLowerCase();
        const roleFilter = els.filterSelect.value;

        const filtered = users.filter(u => {
            const matchSearch = (u.nombre && u.nombre.toLowerCase().includes(searchTerm)) ||
                                (u.apellido && u.apellido.toLowerCase().includes(searchTerm)) ||
                                (u.dni && String(u.dni).includes(searchTerm)) ||
                                (u.correo && u.correo.toLowerCase().includes(searchTerm));
            const matchRole = roleFilter === '' || u.role === roleFilter;
            return matchSearch && matchRole;
        });

        if (filtered.length === 0) {
            els.tableBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No se encontraron usuarios.</td></tr>';
            return;
        }

        els.tableBody.innerHTML = filtered.map(u => `
            <tr>
                <td>${u.dni || '-'}</td>
                <td>${u.apellido}, ${u.nombre}</td>
                <td>${u.telefono || '-'}</td>
                <td>${u.correo || '-'}</td>
                <td><span class="role-badge ${u.role}">${u.role}</span></td>
                <td class="actions-cell">
                    <button class="action-btn" title="Editar" onclick="UserModule.openEditModal(${u.id})">✏️</button>
                    <button class="action-btn" title="Cambiar contraseña" onclick="UserModule.openPasswordModal(${u.id})">🔒</button>
                    <button class="action-btn danger" title="Eliminar" onclick="UserModule.deleteUser(${u.id})">🗑️</button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Modales
     */
    function openCreateModal() {
        els.form.reset();
        els.id.value = '';
        els.modalTitle.textContent = 'Nuevo Usuario';
        els.password.required = true;
        els.passwordHelp.style.display = 'none';
        els.username.disabled = false;
        openModal('userCreateModal');
    }

    function openEditModal(id) {
        const user = users.find(u => u.id === id);
        if (!user) return;

        els.form.reset();
        els.id.value = user.id;
        els.modalTitle.textContent = 'Editar Usuario';
        
        els.nombre.value = user.nombre || '';
        els.apellido.value = user.apellido || '';
        els.dni.value = user.dni || '';
        els.telefono.value = user.telefono || '';
        els.correo.value = user.correo || '';
        els.username.value = user.username || '';
        els.role.value = user.role || 'alumno';

        els.password.required = false;
        els.passwordHelp.style.display = 'block';
        els.username.disabled = true;

        openModal('userCreateModal');
    }

    function openPasswordModal(id) {
        els.pwdForm.reset();
        els.pwdId.value = id;
        openModal('userPasswordModal');
    }

    /**
     * Guardar Usuario (Crear o Actualizar)
     */
    async function saveUser() {
        const id = els.id.value;
        const isEdit = id !== '';
        const url = isEdit ? `/profesor/api/users/${id}` : '/profesor/api/users';
        const method = isEdit ? 'PUT' : 'POST';

        const payload = {
            nombre: els.nombre.value,
            apellido: els.apellido.value,
            dni: els.dni.value,
            telefono: els.telefono.value,
            correo: els.correo.value,
            role: els.role.value,
        };

        if (!isEdit) {
            payload.username = els.username.value;
            payload.password = els.password.value;
        }

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                closeModal('userCreateModal');
                loadUsers();
            } else {
                // Parse validation errors if available
                let errorMsg = data.message;
                if (data.errors) {
                    errorMsg = Object.values(data.errors).map(e => e.join(' ')).join('\n');
                }
                showToast(errorMsg, true);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión al guardar', true);
        }
    }

    /**
     * Guardar nueva contraseña
     */
    async function savePassword() {
        const id = els.pwdId.value;
        const newPassword = els.newPassword.value;

        try {
            const response = await fetch(`/profesor/api/users/${id}/password`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ password: newPassword })
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                closeModal('userPasswordModal');
            } else {
                showToast(data.message || 'Error al actualizar contraseña', true);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', true);
        }
    }

    /**
     * Eliminar usuario
     */
    async function deleteUser(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            return;
        }

        try {
            const response = await fetch(`/profesor/api/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message);
                loadUsers();
            } else {
                showToast(data.message || 'Error al eliminar', true);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', true);
        }
    }

    // Listeners para filtros
    els.searchInput.addEventListener('input', renderTable);
    els.filterSelect.addEventListener('change', renderTable);

    // Inicializar cargando la lista
    document.addEventListener('DOMContentLoaded', () => {
        // Only load if we are on the usuarios module
        if (document.getElementById('userTableBody')) {
            loadUsers();
        }
    });

    // API pública del módulo
    return {
        loadUsers,
        openCreateModal,
        openEditModal,
        openPasswordModal,
        saveUser,
        savePassword,
        deleteUser
    };

})();
