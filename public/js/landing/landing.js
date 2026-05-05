// ============================================================
// KIOSKO-UNER | LANDING PAGE – JS
// ============================================================

(function () {
    'use strict';

    // ── Header scroll effect ──────────────────────────────────
    const header = document.getElementById('landingHeader');

    function updateHeaderOnScroll() {
        if (window.scrollY > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', updateHeaderOnScroll, { passive: true });
    updateHeaderOnScroll();

    // ── Mobile nav toggle ─────────────────────────────────────
    const mobileToggle = document.getElementById('mobileToggle');
    const headerNav    = document.getElementById('headerNav');

    if (mobileToggle && headerNav) {
        mobileToggle.addEventListener('click', () => {
            headerNav.classList.toggle('open');
            mobileToggle.textContent = headerNav.classList.contains('open') ? '✕' : '☰';
        });

        // Close mobile nav on link click
        headerNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                headerNav.classList.remove('open');
                mobileToggle.textContent = '☰';
            });
        });
    }

    // ── Smooth scroll for anchor links ────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const targetId = anchor.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const headerHeight = header.offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    // ── Intersection Observer – Fade-in-up animations ─────────
    const fadeElements = document.querySelectorAll('.fade-in-up');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -40px 0px'
        });

        fadeElements.forEach(el => observer.observe(el));
    } else {
        // Fallback: show all elements immediately
        fadeElements.forEach(el => el.classList.add('visible'));
    }

})();
