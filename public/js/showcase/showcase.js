// ============================================================
// KIOSKO-UNER | SHOWCASE JS
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    /* ── Selector de items (tiles) ─────────────────────── */
    document.querySelectorAll('.item-tile').forEach(tile => {
        tile.addEventListener('click', function () {
            const group = this.closest('.item-grid');
            group.querySelectorAll('.item-tile').forEach(t => t.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    /* ── Progress bar animada al cargar ────────────────── */
    const fills = document.querySelectorAll('.progress-fill[data-value]');
    fills.forEach(fill => {
        const target = parseInt(fill.dataset.value, 10);
        fill.style.width = '0%';
        setTimeout(() => { fill.style.width = target + '%'; }, 300);
    });

    /* ── Range: mostrar valor en tiempo real ───────────── */
    document.querySelectorAll('.form-range[data-output]').forEach(range => {
        const outputId = range.dataset.output;
        const output = document.getElementById(outputId);
        if (!output) return;
        output.textContent = range.value;
        range.addEventListener('input', () => { output.textContent = range.value; });
    });

    /* ── Botón copiar color ─────────────────────────────── */
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const orig = this.textContent;
                this.textContent = '¡Copiado!';
                setTimeout(() => { this.textContent = orig; }, 1500);
            });
        });
    });

    /* ── Ripple en botones ──────────────────────────────── */
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'btn-ripple';
            ripple.style.cssText = `
                position:absolute;
                border-radius:50%;
                transform:scale(0);
                animation:ripple 0.5s linear;
                background:rgba(255,255,255,0.18);
                width:80px; height:80px;
                left:${e.clientX - rect.left - 40}px;
                top:${e.clientY - rect.top - 40}px;
                pointer-events:none;
            `;
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
});
