// ============================================================
// KIOSKO-UNER | LOGIN PAGE – JS
// ============================================================

(function () {
    'use strict';

    const form       = document.getElementById('loginForm');
    const errorBox   = document.getElementById('loginError');
    const errorText  = document.getElementById('loginErrorText');
    const loginInput = document.getElementById('login');
    const passInput  = document.getElementById('password');
    const btnSubmit  = document.getElementById('btnLogin');

    if (!form) return;

    // ── Client-side validation ────────────────────────────────
    form.addEventListener('submit', function (e) {
        // Reset error states
        loginInput.classList.remove('error');
        passInput.classList.remove('error');
        errorBox.classList.remove('visible');

        let hasError = false;

        // Validate login field
        if (!loginInput.value.trim()) {
            loginInput.classList.add('error');
            showError('Por favor, ingresá tu usuario o correo electrónico.');
            hasError = true;
        }

        // Validate password
        if (!passInput.value) {
            passInput.classList.add('error');
            if (!hasError) showError('Por favor, ingresá tu contraseña.');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            return;
        }

        // Show loading state
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Ingresando...';
    });

    // ── Focus clear error ─────────────────────────────────────
    [loginInput, passInput].forEach(input => {
        input.addEventListener('focus', () => {
            input.classList.remove('error');
        });
    });

    // ── Helpers ───────────────────────────────────────────────
    function showError(msg) {
        errorText.textContent = msg;
        errorBox.classList.add('visible');
    }

})();
