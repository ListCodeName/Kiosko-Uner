// ============================================================
// KIOSKO-UNER | PANEL DE CONTROL – JS
// ============================================================

(function () {
    'use strict';

    // ── Elementos ──────────────────────────────────────────
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebarOverlay');
    const btnCollapse    = document.getElementById('btnCollapse');
    const btnHamburger   = document.getElementById('btnHamburger');
    const navItems       = document.querySelectorAll('.nav-item[data-module]');
    const moduleSections = document.querySelectorAll('.module-section');
    const headerTitle    = document.getElementById('headerTitle');
    const headerBadge    = document.getElementById('headerBadge');
    const groupToggles   = document.querySelectorAll('.nav-group-toggle');

    // ── Subgrupos colapsables ───────────────────────────────
    groupToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const group = toggle.closest('.nav-group');
            group.classList.toggle('open');
            // Persistir estado
            const groupId = toggle.dataset.group;
            localStorage.setItem(`group-${groupId}`, group.classList.contains('open'));
        });
    });

    // Restaurar estado de subgrupos
    groupToggles.forEach(toggle => {
        const groupId = toggle.dataset.group;
        const stored  = localStorage.getItem(`group-${groupId}`);
        const group   = toggle.closest('.nav-group');
        if (stored === 'false') group.classList.remove('open');
        else                    group.classList.add('open');
    });


    // ── Sidebar: colapsar/expandir (desktop) ────────────────
    if (btnCollapse) {
        btnCollapse.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Restaurar estado desde localStorage
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    // ── Sidebar: abrir/cerrar (mobile) ───────────────────────
    function openSidebar() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('visible');
        document.body.style.overflow = '';
    }

    if (btnHamburger) btnHamburger.addEventListener('click', openSidebar);
    if (overlay)      overlay.addEventListener('click', closeSidebar);

    // Cerrar sidebar mobile al cambiar a desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeSidebar();
    });

    // ── Navegación de módulos ────────────────────────────────
    function activateModule(moduleId) {
        // Nav items
        navItems.forEach(item => {
            item.classList.toggle('active', item.dataset.module === moduleId);
        });

        // Secciones de contenido
        moduleSections.forEach(section => {
            section.classList.toggle('active', section.dataset.moduleContent === moduleId);
        });

        // Header: actualizar título y badge
        const activeNav = document.querySelector(`.nav-item[data-module="${moduleId}"]`);
        if (activeNav && headerTitle) {
            headerTitle.textContent = activeNav.dataset.title || moduleId;
        }
        if (activeNav && headerBadge) {
            headerBadge.textContent = activeNav.dataset.badge || moduleId;
        }

        // Guardar módulo activo
        localStorage.setItem('active-module', moduleId);

        // Cerrar sidebar mobile al navegar
        if (window.innerWidth <= 768) closeSidebar();
    }

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            activateModule(item.dataset.module);
        });
    });

    // Restaurar módulo activo al recargar
    const savedModule = localStorage.getItem('active-module');
    const firstModule = navItems.length ? navItems[0].dataset.module : null;
    activateModule(savedModule || firstModule);

})();
