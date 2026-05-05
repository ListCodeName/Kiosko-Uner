// ============================================================
// KIOSKO-UNER | PANEL PROFESOR – JS
// ============================================================
(function () {
    'use strict';

    const sidebar      = document.getElementById('sidebar');
    const overlay      = document.getElementById('sidebarOverlay');
    const btnCollapse  = document.querySelector('.sidebar-footer .btn-collapse:not([type="submit"])');
    const btnHamburger = document.getElementById('btnHamburger');
    const navItems     = document.querySelectorAll('.nav-item[data-module]');
    const modules      = document.querySelectorAll('.module-section');
    const headerTitle  = document.getElementById('headerTitle');
    const headerBadge  = document.getElementById('headerBadge');

    // ── Mobile sidebar ────────────────────────────────────────
    function openSidebar()  { sidebar.classList.add('mobile-open');  overlay.classList.add('visible');  document.body.style.overflow='hidden'; }
    function closeSidebar() { sidebar.classList.remove('mobile-open'); overlay.classList.remove('visible'); document.body.style.overflow=''; }
    if (btnHamburger) btnHamburger.addEventListener('click', openSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', () => { if(window.innerWidth>768) closeSidebar(); });

    // ── Module navigation ─────────────────────────────────────
    function activateModule(id) {
        navItems.forEach(i => i.classList.toggle('active', i.dataset.module === id));
        modules.forEach(m => m.classList.toggle('active', m.dataset.moduleContent === id));
        const nav = document.querySelector(`.nav-item[data-module="${id}"]`);
        if (nav && headerTitle) headerTitle.textContent = nav.dataset.title || id;
        if (nav && headerBadge) headerBadge.textContent = nav.dataset.badge || id;
        localStorage.setItem('prof-active-module', id);
        if (window.innerWidth <= 768) closeSidebar();
    }
    navItems.forEach(i => i.addEventListener('click', () => activateModule(i.dataset.module)));
    const saved = localStorage.getItem('prof-active-module');
    activateModule(saved || 'inicio');

    // ── Close modals on overlay click ─────────────────────────
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if(e.target === o) o.classList.remove('visible'); });
    });

    // ── Toast ─────────────────────────────────────────────────
    window.showToast = function(msg, type='success') {
        const t = document.getElementById('toast');
        if(!t) return;
        t.textContent = msg;
        t.className = `toast ${type} visible`;
        setTimeout(() => t.classList.remove('visible'), 3000);
    };

})();
