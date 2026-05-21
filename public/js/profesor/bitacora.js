/**
 * Módulo de Bitácora de Auditoría para el Panel del Profesor
 */

const BitacoraModule = (function () {
    let logs = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Elementos del DOM
    const els = {
        tableBody: document.getElementById('bitacoraTableBody'),
        searchInput: document.getElementById('bitacoraSearch'),
        filterModule: document.getElementById('bitacoraFilterModule'),
        filterAction: document.getElementById('bitacoraFilterAction'),
    };

    /**
     * Generar un color HSL estable y estético basado en el string (nombre)
     */
    function stringToHslColor(str, s = 65, l = 45) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        const h = Math.abs(hash % 360);
        return `hsl(${h}, ${s}%, ${l}%)`;
    }

    /**
     * Obtener las iniciales de un nombre
     */
    function getInitials(name) {
        if (!name) return '?';
        return name.split(' ')
            .map(w => w.charAt(0).toUpperCase())
            .slice(0, 2)
            .join('');
    }

    /**
     * Cargar los registros de logs desde la base de datos
     */
    async function loadLogs() {
        if (!els.tableBody) return;

        try {
            els.tableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
                            <span style="font-size: 2rem; animation: spin 1.5s linear infinite;">⏳</span>
                            <span>Actualizando bitácora de actividades...</span>
                        </div>
                    </td>
                </tr>
            `;

            const response = await fetch('/profesor/api/bitacora');
            const data = await response.json();

            if (response.ok) {
                logs = data.logs || [];
                renderTable();
            } else {
                if (window.showToast) {
                    window.showToast('Error al cargar la bitácora', 'error');
                } else {
                    console.error('Error al cargar logs');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            els.tableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                        ❌ Error de conexión al cargar la bitácora.
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Renderizar la tabla de logs según filtros reactivos
     */
    function renderTable() {
        if (!els.tableBody) return;

        const searchTerm = els.searchInput.value.toLowerCase();
        const moduleFilter = els.filterModule.value;
        const actionFilter = els.filterAction.value;

        const filtered = logs.filter(l => {
            const matchSearch = (l.user_name && l.user_name.toLowerCase().includes(searchTerm)) ||
                                (l.description && l.description.toLowerCase().includes(searchTerm)) ||
                                (l.action && l.action.toLowerCase().includes(searchTerm));
            
            const matchModule = moduleFilter === '' || l.module === moduleFilter;
            
            let matchAction = true;
            if (actionFilter !== '') {
                if (actionFilter === 'INSERT' || actionFilter === 'UPDATE' || actionFilter === 'DELETE') {
                    matchAction = l.action === actionFilter;
                } else {
                    // Es un tipo de acción específico (ej. sale, order_delivery)
                    matchAction = l.action.toLowerCase() === actionFilter.toLowerCase();
                }
            }

            return matchSearch && matchModule && matchAction;
        });

        if (filtered.length === 0) {
            els.tableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        No se encontraron registros de actividades.
                    </td>
                </tr>
            `;
            return;
        }

        els.tableBody.innerHTML = filtered.map(l => {
            // Formatear badges
            const moduleLower = l.module ? l.module.toLowerCase() : '';
            const actionLower = l.action ? l.action.toLowerCase() : '';
            
            let actionClass = 'other';
            if (l.action === 'INSERT') actionClass = 'insert';
            else if (l.action === 'UPDATE') actionClass = 'update';
            else if (l.action === 'DELETE') actionClass = 'delete';
            else if (actionLower.includes('sale') || actionLower.includes('order')) actionClass = 'sale';

            // Formatear Fecha
            const dateObj = l.created_at ? new Date(l.created_at) : null;
            const fullDate = dateObj ? dateObj.toLocaleString('es-AR') : '-';

            // Estilos dinámicos del avatar del alumno
            const avatarColor = stringToHslColor(l.user_name);
            const initials = getInitials(l.user_name);

            return `
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s; background: transparent;" onmouseover="this.style.background='rgba(34,197,94,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div class="avatar-circle" style="background:${avatarColor};">${initials}</div>
                            <div>
                                <div style="font-weight:600; color:var(--text-primary); font-size:0.875rem;">${l.user_name}</div>
                                <div style="font-size:0.75rem; color:var(--text-secondary); text-transform: capitalize;">${l.user_role}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <span class="module-badge ${moduleLower}">${l.module || 'General'}</span>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <span class="action-badge ${actionClass}">${l.action}</span>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle; color:var(--text-primary); font-size:0.875rem; line-height: 1.4; font-weight: 500;">
                        ${l.description || '-'}
                    </td>
                    <td style="padding: 1rem; vertical-align: middle; font-size: 0.8125rem;" title="${fullDate}">
                        <div style="font-weight: 500; color: var(--text-primary);">${l.time_ago}</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.125rem;">${fullDate.split(' ')[0]}</div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Registrar listeners para los filtros
    if (els.searchInput) els.searchInput.addEventListener('input', renderTable);
    if (els.filterModule) els.filterModule.addEventListener('change', renderTable);
    if (els.filterAction) els.filterAction.addEventListener('change', renderTable);

    // Integrar con el despachador de módulos del panel del profesor
    document.addEventListener('DOMContentLoaded', () => {
        // Encontrar los botones de navegación del menú y reaccionar al cambio
        const navButtons = document.querySelectorAll('.sidebar-nav .nav-item');
        navButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const moduleName = this.getAttribute('data-module');
                if (moduleName === 'bitacora') {
                    loadLogs();
                }
            });
        });

        // Carga inicial por si se recarga la página directo en este módulo
        const currentActive = document.querySelector('.sidebar-nav .nav-item.active');
        if (currentActive && currentActive.getAttribute('data-module') === 'bitacora') {
            loadLogs();
        }
    });

    return {
        loadLogs,
        renderTable
    };
})();
