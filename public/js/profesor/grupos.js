(function () {
    'use strict';

    const state = {
        groups: [],
        studentsSearch: []
    };

    const API_URL = '/profesor/api/groups';
    const STUDENTS_API_URL = '/profesor/api/students';

    // Headers para fetch
    const getHeaders = () => {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        };
    };

    async function fetchGroups() {
        try {
            const res = await fetch(API_URL, { headers: getHeaders() });
            if (!res.ok) throw new Error('Error al cargar grupos');
            const groups = await res.json();
            state.groups = groups;
            renderGroups();
            
            // Sincronizar dinámicamente con el selector del módulo de asistencia
            if (window.asistenciaModule && typeof window.asistenciaModule.loadGroups === 'function') {
                window.asistenciaModule.loadGroups();
            }
        } catch (error) {
            console.error(error);
            showToast('Error al cargar grupos', 'error');
        }
    }

    // Renderizar grupos en el DOM
    function renderGroups() {
        const container = document.getElementById('groupsContainer');
        if (!container) return;
        
        const searchTerm = document.getElementById('groupSearch').value.toLowerCase();
        
        let html = '';
        
        const filteredGroups = state.groups.filter(g => 
            g.name.toLowerCase().includes(searchTerm) || 
            (g.description && g.description.toLowerCase().includes(searchTerm))
        );

        if (filteredGroups.length === 0) {
            html = `<div style="grid-column: 1 / -1; text-align: center; color: var(--text-secondary); padding: 2rem;">No se encontraron grupos.</div>`;
        } else {
            filteredGroups.forEach((g, index) => {
                // Seleccionar color de icono (ciclo simple de colores)
                const icons = ['🟢', '🔵', '🟡', '🟣', '🟠'];
                const icon = icons[index % icons.length];
                
                // Generar los chips de los miembros
                let membersHtml = '';
                if (g.students && g.students.length > 0) {
                    g.students.forEach(student => {
                        const initials = getInitials(student.name);
                        membersHtml += `
                            <div class="member-chip">
                                <span class="member-avatar">${initials}</span>
                                <span title="${student.name}">${truncateName(student.name)}</span>
                                <button type="button" class="member-remove" title="Quitar del grupo" onclick="window.gruposModule.removeStudent(${g.id}, ${student.id})">✕</button>
                            </div>
                        `;
                    });
                } else {
                    membersHtml = `<span style="font-size: 0.85rem; color: var(--text-secondary);">Sin alumnos asignados</span>`;
                }

                html += `
                    <div class="group-card">
                        <div class="group-card-header">
                            <div class="group-card-title">
                                <span class="group-icon">${icon}</span>
                                <h3 title="${g.name}">${g.name}</h3>
                            </div>
                            <div class="group-card-actions">
                                <button type="button" class="action-btn" title="Editar grupo" onclick="window.gruposModule.openEditModal(${g.id})">✏️</button>
                                <button type="button" class="action-btn danger" title="Eliminar grupo" onclick="window.gruposModule.deleteGroup(${g.id})">🗑️</button>
                            </div>
                        </div>
                        <div class="group-card-meta">
                            <span class="group-meta-item">👥 ${g.students_count || 0} alumnos</span>
                            ${g.description ? `<span class="group-meta-item" style="opacity:0.8">${g.description}</span>` : ''}
                        </div>
                        <div class="group-members">
                            ${membersHtml}
                        </div>
                        <button type="button" class="group-add-btn" onclick="window.gruposModule.openAddMemberModal(${g.id})">＋ Agregar alumno</button>
                    </div>
                `;
            });
        }
        
        container.innerHTML = html;
    }

    // Funciones Helper
    function getInitials(name) {
        if (!name) return '?';
        return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    }

    function truncateName(name) {
        if (!name) return '';
        if (name.length > 18) return name.substring(0, 15) + '...';
        return name;
    }

    // Exportar módulo a window para llamadas desde HTML
    window.gruposModule = {
        openCreateModal: () => {
            document.getElementById('createGroupName').value = '';
            document.getElementById('createGroupDesc').value = '';
            document.getElementById('groupCreateModal').classList.add('visible');
        },
        
        openEditModal: (id) => {
            const group = state.groups.find(g => g.id === id);
            if (!group) return;
            
            document.getElementById('editGroupId').value = group.id;
            document.getElementById('editGroupName').value = group.name;
            document.getElementById('editGroupDesc').value = group.description || '';
            document.getElementById('groupEditModal').classList.add('visible');
        },

        deleteGroup: async (id) => {
            if (!confirm('¿Estás seguro de eliminar este grupo? Todos los alumnos serán desasignados (pero no eliminados del sistema).')) {
                return;
            }
            
            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: getHeaders()
                });
                if (!res.ok) throw new Error('Error al eliminar');
                showToast('Grupo eliminado', 'success');
                fetchGroups();
            } catch (error) {
                showToast('Error al eliminar grupo', 'error');
            }
        },

        openAddMemberModal: (id) => {
            document.getElementById('addMemberGroupId').value = id;
            document.getElementById('studentSearchInput').value = '';
            document.getElementById('studentSelectList').innerHTML = `<div style="text-align: center; color: var(--text-secondary); padding: 1rem;">Escribe para buscar alumnos...</div>`;
            document.getElementById('groupAddMemberModal').classList.add('visible');
            
            // Cargar estudiantes sin filtro inicialmente
            searchStudents('');
        },

        removeStudent: async (groupId, studentId) => {
            if (!confirm('¿Quitar a este alumno del grupo?')) return;
            
            try {
                const res = await fetch(`${API_URL}/${groupId}/members/${studentId}`, {
                    method: 'DELETE',
                    headers: getHeaders()
                });
                if (!res.ok) throw new Error('Error al quitar alumno');
                showToast('Alumno quitado del grupo', 'success');
                fetchGroups();
            } catch (error) {
                showToast('Error al quitar alumno', 'error');
            }
        }
    };

    // Listeners para los Formularios
    document.addEventListener('DOMContentLoaded', () => {
        
        // Crear Grupo
        const createForm = document.getElementById('groupCreateForm');
        if (createForm) {
            createForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btnSubmit = document.getElementById('btnCreateGroupSubmit');
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Guardando...';

                const payload = {
                    name: document.getElementById('createGroupName').value,
                    description: document.getElementById('createGroupDesc').value
                };

                try {
                    const res = await fetch(API_URL, {
                        method: 'POST',
                        headers: getHeaders(),
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) throw new Error('Error al crear grupo');
                    
                    document.getElementById('groupCreateModal').classList.remove('visible');
                    showToast('Grupo creado correctamente', 'success');
                    fetchGroups();
                } catch (error) {
                    showToast('Error al crear el grupo', 'error');
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Crear Grupo';
                }
            });
        }

        // Editar Grupo
        const editForm = document.getElementById('groupEditForm');
        if (editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const btnSubmit = document.getElementById('btnEditGroupSubmit');
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Guardando...';

                const id = document.getElementById('editGroupId').value;
                const payload = {
                    name: document.getElementById('editGroupName').value,
                    description: document.getElementById('editGroupDesc').value
                };

                try {
                    const res = await fetch(`${API_URL}/${id}`, {
                        method: 'PUT',
                        headers: getHeaders(),
                        body: JSON.stringify(payload)
                    });
                    if (!res.ok) throw new Error('Error al editar grupo');
                    
                    document.getElementById('groupEditModal').classList.remove('visible');
                    showToast('Grupo actualizado correctamente', 'success');
                    fetchGroups();
                } catch (error) {
                    showToast('Error al editar el grupo', 'error');
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Guardar Cambios';
                }
            });
        }

        // Agregar Alumno (Búsqueda)
        const searchInput = document.getElementById('studentSearchInput');
        if (searchInput) {
            let timeout = null;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    searchStudents(e.target.value);
                }, 300);
            });
        }

        // Agregar Alumno (Confirmar seleccionados)
        const btnAddMemberSubmit = document.getElementById('btnAddMemberSubmit');
        if (btnAddMemberSubmit) {
            btnAddMemberSubmit.addEventListener('click', async () => {
                const groupId = document.getElementById('addMemberGroupId').value;
                const checkboxes = document.querySelectorAll('.student-checkbox:checked');
                
                if (checkboxes.length === 0) {
                    showToast('Debes seleccionar al menos un alumno', 'error');
                    return;
                }
                
                btnAddMemberSubmit.disabled = true;
                btnAddMemberSubmit.textContent = 'Agregando...';

                try {
                    // Como el endpoint addStudent solo recibe de a uno por vez, 
                    // o hacemos peticiones múltiples, o modificamos la logica en el backend.
                    // En nuestro backend lo hicimos user_id => single user. Haremos map con Promise.all
                    
                    const promises = Array.from(checkboxes).map(chk => {
                        return fetch(`${API_URL}/${groupId}/members`, {
                            method: 'POST',
                            headers: getHeaders(),
                            body: JSON.stringify({ user_id: chk.value })
                        });
                    });

                    await Promise.all(promises);
                    
                    document.getElementById('groupAddMemberModal').classList.remove('visible');
                    showToast('Alumno(s) agregado(s) con éxito', 'success');
                    fetchGroups();
                } catch (error) {
                    showToast('Error al agregar alumnos', 'error');
                } finally {
                    btnAddMemberSubmit.disabled = false;
                    btnAddMemberSubmit.textContent = 'Confirmar';
                }
            });
        }

        // Búsqueda en el módulo principal
        const groupSearch = document.getElementById('groupSearch');
        if (groupSearch) {
            groupSearch.addEventListener('input', renderGroups);
        }

        // Carga inicial
        fetchGroups();
    });

    // Función para buscar estudiantes desde la API
    async function searchStudents(query) {
        const listContainer = document.getElementById('studentSelectList');
        listContainer.innerHTML = `<div style="text-align: center; color: var(--text-secondary); padding: 1rem;">Buscando...</div>`;
        
        try {
            const res = await fetch(`${STUDENTS_API_URL}?q=${encodeURIComponent(query)}`, { headers: getHeaders() });
            if (!res.ok) throw new Error('Error en búsqueda');
            const students = await res.json();
            
            // Filtro local: Evitar mostrar los que ya están en el grupo seleccionado
            const groupId = document.getElementById('addMemberGroupId').value;
            const currentGroup = state.groups.find(g => g.id == groupId);
            const currentStudentIds = currentGroup && currentGroup.students ? currentGroup.students.map(s => s.id) : [];
            
            const availableStudents = students.filter(s => !currentStudentIds.includes(s.id));

            if (availableStudents.length === 0) {
                listContainer.innerHTML = `<div style="text-align: center; color: var(--text-secondary); padding: 1rem;">No hay alumnos disponibles.</div>`;
                return;
            }

            let html = '';
            availableStudents.forEach(s => {
                const initials = getInitials(s.name);
                html += `
                    <label class="member-select-item">
                        <input type="checkbox" class="student-checkbox" value="${s.id}"> 
                        <span class="member-avatar" style="width:24px;height:24px;font-size:.6rem">${initials}</span> 
                        ${s.name} ${s.username ? `<span style="color:var(--text-secondary); font-size:0.8rem">(${s.username})</span>` : ''}
                    </label>
                `;
            });
            listContainer.innerHTML = html;
        } catch (error) {
            listContainer.innerHTML = `<div style="text-align: center; color: var(--error-color); padding: 1rem;">Error al buscar.</div>`;
        }
    }

})();
